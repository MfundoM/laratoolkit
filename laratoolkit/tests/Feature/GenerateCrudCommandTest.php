<?php

namespace Laratoolkit\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Artisan;

class GenerateCrudCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \Laratoolkit\LaratoolkitServiceProvider::class,
        ];
    }

    /** @test */
    public function it_generates_crud_files_for_model()
    {
        // Prepare the route file
        $webRoutePath = base_path('routes/web.php');

        if (!file_exists($webRoutePath)) {
            file_put_contents($webRoutePath, "<?php\n\n");
        }

        // Define the route stub (you can improve this later)
        $routeStub = "\nRoute::resource('books', App\\Http\\Controllers\\BookController::class);";

        file_put_contents($webRoutePath, $routeStub, FILE_APPEND);

        // Run your command
        $exitCode = Artisan::call('generate:crud', [
            'name' => 'Book',
            //'--inertia' => 'vue',
        ]);

        $this->assertEquals(0, $exitCode);

        // Assertions
        $this->assertFileExists(app_path('Models/Book.php'));
        $this->assertFileExists(app_path('Http/Controllers/BookController.php'));
        $this->assertFileExists(app_path('Services/BookService.php'));
        $this->assertFileExists(app_path('Http/Requests/BookRequest.php'));
        // $this->assertFileExists(app_path('Http/Requests/BaseRequest.php'));
        $this->assertDirectoryExists(base_path('database/migrations'));
        $this->assertFileExists(resource_path('views/books/index.blade.php'));
    }
}
