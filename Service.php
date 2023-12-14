<?php

namespace App\Services\Contabo;

use App\Services\ServiceInterface;
use App\Models\Package;
use App\Models\Order;

class Service implements ServiceInterface
{
    /**
     * Unique key used to store settings 
     * for this service.
     * 
     * @return string
     */
    public static $key = 'contabo'; 

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    
    /**
     * Returns the meta data about this Server/Service
     *
     * @return object
     */
    public static function metaData(): object
    {
        return (object)
        [
          'display_name' => 'Contabo',
          'author' => 'WemX',
          'version' => '1.0.0',
          'wemx_version' => ['dev', '>=1.8.0'],
        ];
    }

    /**
     * Define the default configuration values required to setup this service
     * i.e host, api key, or other values. Use Laravel validation rules for
     *
     * Laravel validation rules: https://laravel.com/docs/10.x/validation
     *
     * @return array
     */
    public static function setConfig(): array
    {
        return [
            [
                "key" => "contabo::client_id",
                "name" => "Client ID",
                "description" => "ClientId of your contabo account",
                "type" => "text",
                "rules" => ['required'], // laravel validation rules
            ],
            [
                "key" => "encrypted::contabo::client_secret",
                "name" => "Client Secret",
                "description" => "Client Secret of your contabo account",
                "type" => "password",
                "rules" => ['required'], // laravel validation rules
            ],
            [
                "key" => "contabo::username",
                "name" => "Contabo Username",
                "description" => "Username of your contabo account",
                "type" => "email",
                "rules" => ['required', 'email'], // laravel validation rules
            ],
            [
                "key" => "encrypted::contabo::user_password",
                "name" => "Contabo Account Password",
                "description" => "Password of your contabo account",
                "type" => "password",
                "rules" => ['required'], // laravel validation rules
            ],
        ];
    }

    /**
     * Define the default package configuration values required when creatig
     * new packages. i.e maximum ram usage, allowed databases and backups etc.
     *
     * Laravel validation rules: https://laravel.com/docs/10.x/validation
     *
     * @return array
     */
    public static function setPackageConfig(Package $package): array
    {
        return [
            [
                "col" => "col-12",
                "key" => "product",
                "name" => "Product ID",
                "description" => "ID of the contabo product",
                "type" => "select",
                "options" => [
                    'V1' => 'VPS S SSD (200 GB SSD)',
                    'V35' => 'VPS S Storage (400 GB SSD)',
                    'V12' => 'VPS S NVMe (50 GB NVMe)',
                    'V2' => 'VPS M SSD (400 GB SSD)',
                    'V36' => 'VPS M Storage (800 GB SSD)',
                    'V13' => 'VPS M NVMe (100 GB NVMe)',
                    'V3' => 'VPS L SSD (800 GB SSD)',
                    'V37' => 'VPS L Storage (1600 GB SSD)',
                    'V14' => 'VPS L NVMe (200 GB NVMe)',
                    'V4' => 'VPS XL SSD (1600 GB SSD)',
                    'V38' => 'VPS XL Storage (3200 GB SSD)',
                    'V15' => 'VPS XL NVMe (400 GB NVMe)',
                    'V42' => 'VPS XXXL SSD (2400 GB SSD)',
                    'V44' => 'VPS XXXL Storage (4800 GB SSD)',
                    'V43' => 'VPS XXXL NVMe (600 GB NVMe)',
                    'V45' => 'VPS 1 SSD (400 GB SSD)',
                    'V46' => 'VPS 1 NVMe (100 GB NVMe)',
                    'V48' => 'VPS 2 SSD (400 GB SSD)',
                    'V49' => 'VPS 2 NVMe (200 GB NVMe)',
                    'V51' => 'VPS 3 SSD (1200 GB SSD)',
                    'V52' => 'VPS 3 NVMe (300 GB NVMe)',
                    'V54' => 'VPS 4 SSD (1600 GB SSD)',
                    'V55' => 'VPS 4 NVMe (400 GB NVMe)',
                    'V57' => 'VPS 5 SSD (2000 GB SSD)',
                    'V58' => 'VPS 5 NVMe (500 GB NVMe)',
                    'V60' => 'VPS 6 SSD (2400 GB SSD)',
                    'V61' => 'VPS 6 NVMe (600 GB NVMe)',
                    'V8' => 'VDS S (180 GB NVMe)',
                    'V9' => 'VDS M (240 GB NVMe)',
                    'V10' => 'VDS L (360 GB NVMe)',
                    'V11' => 'VDS XL (480 GB NVMe)',
                    'V16' => 'VDS XXL (720 GB NVMe)',
                ],
                "default_value" => "V1",
                "rules" => ['required'],
            ],
            [
                "col" => "col-12",
                "key" => "region[]",
                "name" => "Allowed Regions",
                "description" => "Allowed regions for this package at checkout by the user",
                "type" => "select",
                "options" => [
                    'EU' => 'Germany (Europe)',
                    'UK' => 'United Kingdom (Europe)',
                    'US-central' => 'United States (Central)',
                    'US-east' => 'United States (East)',
                    'US-west' => 'United States (West)',
                    'SIN' => 'Singapore (Asia)',
                    'AUS' => 'Australia (Oceania)',
                    'JPN' => 'Japan (Asia)',
                ],
                "multiple" => true,
                "default_value" => "EU",
                "rules" => ['required'],
            ],
        ];
    }

    /**
     * Define the checkout config that is required at checkout and is fillable by
     * the client. Its important to properly sanatize all inputted data with rules
     *
     * Laravel validation rules: https://laravel.com/docs/10.x/validation
     *
     * @return array
     */
    public static function setCheckoutConfig(Package $package): array
    {
        return
        [
            // TO DO: Load the regions from the package config
            [
                "key" => "region",
                "name" => "Region",
                "description" => "Select the region for your server",
                "type" => "select",
                "options" => [
                    'EU' => 'Germany (Europe)',
                    'UK' => 'United Kingdom (Europe)',
                    'US-central' => 'United States (Central)',
                    'US-east' => 'United States (East)',
                    'US-west' => 'United States (West)',
                    'SIN' => 'Singapore (Asia)',
                    'AUS' => 'Australia (Oceania)',
                    'JPN' => 'Japan (Asia)',
                ],
                "default_value" => "EU",
                "rules" => ['required'],
            ],

            // To Do: load OS types / images from API
            [
                "key" => "image",
                "name" => "Image",
                "description" => "Select the image for your server",
                "type" => "select",
                "options" => [
                    '04e0f898-37b4-48bc-a794-1a57abe6aa31' => 'Ubuntu 20.04',
                ],
                "default_value" => "04e0f898-37b4-48bc-a794-1a57abe6aa31",
                "rules" => ['required'],
            ],
        ];
    }

    /**
     * Test API connection
     */
    public static function testConnection()
    {
        try {
            contabo()->getServers();
        } catch(\Exception $error) {
            return redirect()->back()->withError("Failed to connect to Contabo. <br><br>{$error->getMessage()}");
        }

        return redirect()->back()->withSuccess("Successfully connected with Contabo API");
    }

    /**
     * Define buttons shown at order management page
     *
     * @return array
     */
    public static function setServiceButtons(Order $order): array
    {
        return [];    
    }

    /**
     * This function is responsible for creating an instance of the
     * service. This can be anything such as a server, vps or any other instance.
     * 
     * @return void
     */
    public function create(array $data = [])
    {
        return [];
    }

    /**
     * This function is responsible for upgrading or downgrading
     * an instance of this service. This method is optional
     * If your service doesn't support upgrading, remove this method.
     * 
     * Optional
     * @return void
    */
    public function upgrade(Package $oldPackage, Package $newPackage)
    {
        return [];
    }

    /**
     * This function is responsible for suspending an instance of the
     * service. This method is called when a order is expired or
     * suspended by an admin
     * 
     * @return void
    */
    public function suspend(array $data = [])
    {
        return [];
    }

    /**
     * This function is responsible for unsuspending an instance of the
     * service. This method is called when a order is activated or
     * unsuspended by an admin
     * 
     * @return void
    */
    public function unsuspend(array $data = [])
    {
        return [];
    }

    /**
     * This function is responsible for deleting an instance of the
     * service. This can be anything such as a server, vps or any other instance.
     * 
     * @return void
    */
    public function terminate(array $data = [])
    {
        return [];
    }
}
