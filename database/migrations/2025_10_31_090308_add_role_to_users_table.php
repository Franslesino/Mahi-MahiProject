public function up(): void
{
Schema::table('users', function (Blueprint $table) {
$table->string('role')->default('user');
});
}

public function down(): void
{
Schema::table('users', function (Blueprint $table) {
$table->dropColumn('role');
});
}