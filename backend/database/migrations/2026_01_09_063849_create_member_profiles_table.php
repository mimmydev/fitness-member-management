<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates member_profiles table with normalized structure.
     * Separates authentication data (users table) from business data (member_profiles).
     * All foreign keys are indexed for optimal query performance.
     */
    public function up(): void
    {
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();

            // Foreign key to users table (1:1 relationship)
            // Indexed automatically by foreignId()
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Member business information
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();

            // Address information
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();

            // Membership information
            $table->date('membership_start_date');
            $table->date('membership_end_date')->nullable();
            $table->enum('membership_status', ['active', 'inactive', 'suspended', 'expired'])
                ->default('active');
            $table->enum('membership_type', ['basic', 'premium', 'vip'])
                ->default('basic');

            // Emergency contact
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Soft delete for audit trail

            // Additional indexes for common queries
            $table->index('membership_status'); // Filter by status
            $table->index('membership_type'); // Filter by type
            $table->index(['last_name', 'first_name']); // Search by name
            $table->index('membership_end_date'); // Find expiring memberships
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};
