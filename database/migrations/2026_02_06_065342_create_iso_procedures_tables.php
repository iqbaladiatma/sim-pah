<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Kendaraan (Registration & Administration)
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plate_number')->unique();
            $table->string('type'); // Mobil, Motor, Bus, dll
            $table->string('brand')->nullable();
            $table->integer('year')->nullable();
            $table->date('stnk_expiry')->nullable();
            $table->date('tax_expiry')->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'retired'])->default('available');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // 2. Pengajuan Kendaraan (Requests)
        Schema::create('vehicle_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('destination');
            $table->text('purpose');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('start_mileage')->nullable();
            $table->integer('end_mileage')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        // 3. Maintenance Logs (Maintenance, Repair, Cleaning, Garden, Power Outage, Projects)
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'maintenance',
                'repair',
                'project',
                'cleaning',
                'garden',
                'power_outage',
                'vehicle'
            ]);
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete(); // If for vehicle
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('photo_before')->nullable();
            $table->string('photo_after')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'ongoing', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // 4. ISO Checklists
        Schema::create('iso_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // e.g., 'Kebersihan', 'Keamanan', 'Sarpras'
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly']);
            $table->json('items'); // List of checklist items
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. ISO Checklist Executions
        Schema::create('iso_checklist_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iso_checklist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('results'); // {item_id: boolean/text}
            $table->text('notes')->nullable();
            $table->string('photo_evidence')->nullable();
            $table->date('checked_at');
            $table->timestamps();
        });

        // 6. Borrowing Records (Peminjaman Barang)
        Schema::create('borrowing_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->dateTime('borrow_date');
            $table->dateTime('expected_return_date')->nullable();
            $table->dateTime('actual_return_date')->nullable();
            $table->enum('status', ['pending', 'borrowed', 'returned', 'lost', 'damaged', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Asset Lifecycle (Auction, Disposal)
        Schema::create('asset_lifecycle_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['auction', 'disposal']);
            $table->date('action_date');
            $table->decimal('value', 15, 2)->default(0); // If auction, the price
            $table->text('reason');
            $table->string('document_evidence')->nullable();
            $table->timestamps();
        });

        // 8. Parking Logs
        Schema::create('parking_logs', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type'); // Mobil, Motor
            $table->string('plate_number');
            $table->string('owner_name')->nullable();
            $table->dateTime('entry_time');
            $table->dateTime('exit_time')->nullable();
            $table->string('gate_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parking_logs');
        Schema::dropIfExists('asset_lifecycle_logs');
        Schema::dropIfExists('borrowing_records');
        Schema::dropIfExists('iso_checklist_logs');
        Schema::dropIfExists('iso_checklists');
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('vehicle_requests');
        Schema::dropIfExists('vehicles');
    }
};
