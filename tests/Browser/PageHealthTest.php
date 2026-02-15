<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PageHealthTest extends DuskTestCase
{
    /**
     * Test the web login flow and verify the integrity of core school modules.
     */
    public function testWebLoginAndModuleAccess(): void
    {
        $this->browse(function (Browser $browser) {
            $baseUrl = 'http://127.0.0.1:8080';

            // 1. Visit the root URL (where the showLoginForm route is mapped)
            $browser->visit($baseUrl . '/')
                    ->assertTitleContains('Login')
                    ->waitFor('.btn-azure', 5);

            echo "\n--- TESTING WEB LOGIN FORM ---\n";

            // 2. Perform UI Login using identified CSS selectors
            // Using admin / 123456 as confirmed working in web browser
            $browser->type('username', 'admin')
                    ->type('password', '123456')
                    ->click('.btn-azure') 
                    ->waitForLocation('/dashboard', 10);

            if ($browser->driver->getCurrentURL() == $baseUrl . '/dashboard') {
                echo "✅ LOGIN SUCCESSFUL: Dashboard reached.\n";
            } else {
                echo "❌ LOGIN FAILED: Did not reach dashboard. Current URL: " . $browser->driver->getCurrentURL() . "\n";
                $browser->screenshot('login_failure');
                return;
            }

            // 3. Scan the critical modules for "Page Issues"
            $modules = [
                'Staff List'         => '/staff',
                'Library Inventory'  => '/library/books',
                'Student Admissions' => '/admissions',
            ];

            echo "\n--- STARTING MODULE HEALTH CHECK ---\n";

            foreach ($modules as $name => $path) {
                $browser->visit($baseUrl . $path);
                
                // Check if the page contains Laravel error signatures
                $pageSource = $browser->driver->getPageSource();
                $hasError = str_contains($pageSource, '500') || 
                            str_contains($pageSource, 'SQLSTATE') || 
                            str_contains($pageSource, 'Exception');

                if ($hasError) {
                    echo "❌ FAILED: $name ($path) - Check screenshots/error_" . strtolower(str_replace(' ', '_', $name)) . ".png\n";
                    $browser->screenshot('error_' . strtolower(str_replace(' ', '_', $name)));
                } else {
                    echo "✅ HEALTHY: $name ($path)\n";
                }
            }
        });
    }
}
