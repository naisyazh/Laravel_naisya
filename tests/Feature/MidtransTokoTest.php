<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransTokoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_lookup_active_book_from_toko_buku_pos(): void
    {
        $vendor = User::factory()->create([
            'role' => 'admin',
        ]);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        Barang::create([
            'id_barang' => 'BRG90011',
            'nama' => 'Laut Bercerita',
            'harga' => 99000,
            'vendor_id' => $vendor->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->getJson(route('toko-buku.lookup', [
            'kode' => 'BRG90011',
        ]));

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.kode', 'BRG90011')
            ->assertJsonPath('data.nama', 'Laut Bercerita')
            ->assertJsonPath('data.harga', 99000);
    }

    public function test_user_checkout_creates_pending_order_and_snap_token(): void
    {
        config()->set('services.payment_demo.enabled', false);
        config()->set('services.midtrans.server_key', 'SB-Mid-server-test');
        config()->set('services.midtrans.client_key', 'SB-Mid-client-test');
        config()->set('services.midtrans.is_production', false);
        config()->set('services.midtrans.enabled_payments', 'qris');

        $vendor = User::factory()->create([
            'role' => 'admin',
        ]);

        Barang::create([
            'id_barang' => 'BRG90001',
            'nama' => 'Atomic Habits',
            'harga' => 18000,
            'vendor_id' => $vendor->id,
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'user',
            'is_guest' => false,
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/example',
            ], 201),
        ]);

        $response = $this->actingAs($user)->postJson(route('toko-buku.checkout'), [
            'items' => [
                ['kode' => 'BRG90001', 'jumlah' => 2],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.snap_token', 'snap-token-123')
            ->assertJsonPath('data.payment_status', 'pending');

        $order = Penjualan::query()->with('user')->firstOrFail();

        $this->assertSame($vendor->id, $order->vendor_id);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('snap-token-123', $order->snap_token);
        $this->assertSame($user->id, $order->user_id);
        $this->assertFalse((bool) $order->user->is_guest);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions'
                && $request['enabled_payments'] === ['qris'];
        });
        $this->assertDatabaseHas('penjualan_items', [
            'penjualan_id' => $order->id,
            'barang_id' => 'BRG90001',
            'jumlah' => 2,
            'subtotal' => 36000,
        ]);
    }

    public function test_midtrans_notification_marks_order_as_paid(): void
    {
        config()->set('services.payment_demo.enabled', false);
        config()->set('services.midtrans.server_key', 'SB-Mid-server-test');
        config()->set('services.midtrans.client_key', 'SB-Mid-client-test');

        $vendor = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'user',
        ]);

        $order = Penjualan::create([
            'nomor_transaksi' => 'TRX-DEMO-1001',
            'user_id' => $customer->id,
            'vendor_id' => $vendor->id,
            'customer_name' => 'Customer Demo',
            'customer_phone' => '081122334455',
            'total' => 36000,
            'payment_status' => 'pending',
        ]);

        $grossAmount = '36000.00';
        $signature = hash('sha512', $order->nomor_transaksi . '200' . $grossAmount . 'SB-Mid-server-test');

        $response = $this->postJson(route('payments.midtrans.notification'), [
            'order_id' => $order->nomor_transaksi,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'trx-midtrans-001',
            'status_message' => 'Settlement success',
            'signature_key' => $signature,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid');

        $this->assertDatabaseHas('penjualans', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'payment_type' => 'qris',
            'midtrans_transaction_status' => 'settlement',
            'midtrans_transaction_id' => 'trx-midtrans-001',
        ]);
    }

    public function test_refresh_status_endpoint_syncs_midtrans_status(): void
    {
        config()->set('services.payment_demo.enabled', false);
        config()->set('services.midtrans.server_key', 'SB-Mid-server-test');
        config()->set('services.midtrans.client_key', 'SB-Mid-client-test');
        config()->set('services.midtrans.is_production', false);

        $vendor = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'user',
        ]);

        $order = Penjualan::create([
            'nomor_transaksi' => 'TRX-DEMO-2002',
            'user_id' => $customer->id,
            'vendor_id' => $vendor->id,
            'customer_name' => 'Customer Sync',
            'customer_phone' => '081100220033',
            'total' => 54000,
            'payment_status' => 'pending',
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/v2/TRX-DEMO-2002/status' => Http::response([
                'order_id' => $order->nomor_transaksi,
                'transaction_status' => 'settlement',
                'payment_type' => 'qris',
                'status_message' => 'Payment settled',
                'gross_amount' => '54000.00',
                'transaction_id' => 'trx-midtrans-002',
                'settlement_time' => now()->format('Y-m-d H:i:s'),
            ], 200),
        ]);

        $response = $this->actingAs($customer)->postJson(route('toko-buku.orders.refresh', $order->nomor_transaksi));

        $response
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.payment_status_label', 'Lunas');

        $this->assertDatabaseHas('penjualans', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'payment_type' => 'qris',
            'midtrans_transaction_status' => 'settlement',
        ]);
    }

    public function test_vendor_can_view_owned_order_detail_and_refresh_status(): void
    {
        config()->set('services.payment_demo.enabled', false);
        config()->set('services.midtrans.server_key', 'SB-Mid-server-test');
        config()->set('services.midtrans.client_key', 'SB-Mid-client-test');
        config()->set('services.midtrans.is_production', false);

        $vendor = User::factory()->create([
            'role' => 'admin',
        ]);

        $customer = User::factory()->create([
            'role' => 'user',
        ]);

        $order = Penjualan::create([
            'nomor_transaksi' => 'TRX-DEMO-3003',
            'user_id' => $customer->id,
            'vendor_id' => $vendor->id,
            'customer_name' => 'Customer Vendor',
            'customer_email' => 'customer-vendor@example.test',
            'total' => 75000,
            'payment_status' => 'pending',
        ]);

        $viewResponse = $this->actingAs($vendor)->get(route('vendor.orders.show', $order->nomor_transaksi));

        $viewResponse
            ->assertOk()
            ->assertSee('Detail Transaksi Vendor')
            ->assertSee($order->nomor_transaksi)
            ->assertSee('Customer Vendor');

        Http::fake([
            'https://app.sandbox.midtrans.com/v2/TRX-DEMO-3003/status' => Http::response([
                'order_id' => $order->nomor_transaksi,
                'transaction_status' => 'settlement',
                'payment_type' => 'qris',
                'status_message' => 'Payment settled',
                'gross_amount' => '75000.00',
                'transaction_id' => 'trx-midtrans-003',
                'settlement_time' => now()->format('Y-m-d H:i:s'),
            ], 200),
        ]);

        $refreshResponse = $this->actingAs($vendor)->postJson(route('vendor.orders.refresh', $order->nomor_transaksi));

        $refreshResponse
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.payment_status_label', 'Lunas');

        $this->assertDatabaseHas('penjualans', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'payment_type' => 'qris',
        ]);
    }

    public function test_manual_demo_checkout_can_move_from_pending_to_processing_to_paid(): void
    {
        config()->set('services.payment_demo.enabled', true);
        config()->set('services.payment_demo.bank_name', 'BCA');
        config()->set('services.payment_demo.account_number', '9876543210');
        config()->set('services.payment_demo.account_name', 'Demo Pemilik Rekening');
        config()->set('services.payment_demo.note', 'Transfer demo presentasi');

        $vendor = User::factory()->create([
            'role' => 'admin',
        ]);

        Barang::create([
            'id_barang' => 'BRG91111',
            'nama' => 'Filosofi Teras',
            'harga' => 88000,
            'vendor_id' => $vendor->id,
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $checkoutResponse = $this->actingAs($user)->postJson(route('toko-buku.checkout'), [
            'items' => [
                ['kode' => 'BRG91111', 'jumlah' => 1],
            ],
        ]);

        $checkoutResponse
            ->assertCreated()
            ->assertJsonPath('data.payment_mode', 'manual_demo')
            ->assertJsonPath('data.redirect_only', true)
            ->assertJsonPath('data.payment_status', 'pending');

        $order = Penjualan::query()->firstOrFail();

        $this->assertSame('manual_transfer_demo', $order->payment_type);
        $this->assertSame('pending', $order->payment_status);
        $this->assertSame('9876543210', data_get($order->payment_payload, 'bank_details.account_number'));

        $confirmResponse = $this->actingAs($user)->postJson(route('toko-buku.orders.confirm-demo-payment', $order->nomor_transaksi));

        $confirmResponse
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'processing')
            ->assertJsonPath('data.payment_status_label', 'Sedang Diproses');

        $markPaidResponse = $this->actingAs($vendor)->postJson(route('vendor.orders.mark-paid', $order->nomor_transaksi));

        $markPaidResponse
            ->assertOk()
            ->assertJsonPath('data.payment_status', 'paid')
            ->assertJsonPath('data.payment_status_label', 'Lunas');

        $this->assertDatabaseHas('penjualans', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'payment_type' => 'manual_transfer_demo',
        ]);
    }
}
