<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // disable middleware so authentication/authorization doesn't block our
        // simple feature tests. the route names sometimes aren't available
        // inside the testing environment when cached, so we'll hit the URI
        $this->withoutMiddleware();
        // ensure route cache cleared (in case tests were run earlier)
        \Artisan::call('route:clear');
    }

    /** @test */
    public function it_deletes_a_setting_file_and_clears_database_column()
    {
        // create settings row with a fake file path
        $path = 'settings/logo/fake.jpg';

        // ensure directory exists under public
        $full = public_path($path);
        @mkdir(dirname($full), 0755, true);
        file_put_contents($full, 'dummy');
        $this->assertFileExists($full);

        $settings = Setting::create([
            'site_title' => 'Test',
            'app_name'   => 'TestApp',
            'logo'       => $path,
        ]);

        // hit the route as a DELETE request
        $response = $this->deleteJson('/admin/settings/system/file-delete', [
            'field' => 'logo',
        ]);

        $response->assertJson(['success' => true]);

        $settings->refresh();
        $this->assertNull($settings->logo);
        $this->assertFileDoesNotExist($full);
    }

    /** @test */
    public function delete_route_rejects_invalid_field()
    {
        Setting::create([
            'site_title' => 'Test',
            'app_name'   => 'TestApp',
        ]);
        $response = $this->deleteJson('/admin/settings/system/file-delete', [
            'field' => 'bad',
        ]);
        // controller treats unknown field as "no file" and returns 404, which is
        // acceptable for our purposes. just make sure we get an error status.
        $this->assertTrue(
            in_array($response->status(), [404, 422]),
            'expected 404 or 422, got '.$response->status()
        );
    }
}
