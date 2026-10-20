<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Leads Table
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('company_name');
            $table->string('industry', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('source', 50)->default('Website');
            $table->date('entry_date')->default(now());
            $table->foreignId('sales_id')->nullable()->constrained('users')->nullOnDelete();

            // Primary PIC
            $table->string('pic_name', 150);
            $table->string('pic_title', 100)->nullable();
            $table->string('pic_phone', 50);
            $table->string('pic_email', 150)->nullable();

            // Multiple PICs (Stored as JSON array)
            $table->json('additional_pics')->nullable();

            // Business Needs
            $table->string('deal_type', 50)->default('rental'); // rental, sales, both
            $table->string('product_interest', 150)->nullable();
            $table->string('specification', 255)->nullable();
            $table->integer('quantity')->default(1);
            $table->string('rental_duration', 100)->nullable();
            $table->string('project_location', 255)->nullable();
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('estimated_value', 15, 2)->default(0);
            $table->date('expected_closing_date')->nullable();
            $table->text('customer_notes')->nullable();

            // Status & Priority
            $table->string('pipeline_stage', 50)->default('new'); // new, contacted, qualified, proposal, negotiation, won, lost
            $table->string('priority', 50)->default('normal'); // low, normal, high
            $table->text('internal_notes')->nullable();

            // Conversions
            $table->foreignId('converted_customer_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('converted_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();

            $table->timestamps();

            $table->index(['pipeline_stage', 'sales_id']);
            $table->index('entry_date');
        });

        // 2. Lead Activities Table
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type', 50); // Telepon, WhatsApp, Email, Meeting, Kunjungan, Brosur Terkirim, Company Profile Terkirim, Follow-up, Catatan Lainnya
            $table->dateTime('activity_date')->default(now());
            $table->string('summary', 255);
            $table->text('communication_notes')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'activity_date']);
        });

        // 3. Lead Follow Ups Table
        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('follow_up_type', 50); // Telepon, WhatsApp, Email, Meeting, Kunjungan, Kirim Penawaran, Lainnya
            $table->dateTime('scheduled_at');
            $table->text('purpose_notes');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('completed_at')->nullable();
            $table->text('result_notes')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'status', 'scheduled_at']);
        });

        // 4. Lead Stage Histories Table
        Schema::create('lead_stage_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_stage', 50);
            $table->string('to_stage', 50);
            $table->text('change_reason')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['lead_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_stage_histories');
        Schema::dropIfExists('lead_follow_ups');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
    }
};
