<?php

namespace Database\Seeders;

use DB;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\AdministrativeUnit;
use App\Models\WebApplication;
use App\Models\AdministrativeUnitDepartment;
use App\Models\Service;
use App\Models\AnnounceCategory;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        User::firstOrCreate([
            'username' => 'superAdmin'
        ], [
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin123#'),
            'isActive' => true,
            'role' => 'admin'
        ]);

        $units = ['Valea Crișului', 'Bodoc', 'Dalnic', 'Ilieni', 'Brateș'];

        // Adding to Web Applications the GlobImp and the RegAgr
        $globImpApp = WebApplication::firstOrCreate([
            'webApplicationName' => 'GlobImp'
        ], [
            'webApplicationType' => 1
        ]);

        $regAgrApp = WebApplication::firstOrCreate([
            'webApplicationName' => 'RegAgr'
        ], [
            'webApplicationType' => 1
        ]);

        DB::table('web_application_services')->insert([
            ['webApplicationId' => 1, 'id' => 3, 'webServiceName' => 'existsCnpCui'],
            ['webApplicationId' => 1, 'id' => 4, 'webServiceName' => 'persoane'],
            ['webApplicationId' => 1, 'id' => 5, 'webServiceName' => 'terenuri'],
            ['webApplicationId' => 1, 'id' => 6, 'webServiceName' => '.....'],
            ['webApplicationId' => 1, 'id' => 7, 'webServiceName' => '.....'],
            ['webApplicationId' => 2, 'id' => 8, 'webServiceName' => '.....'],
            ['webApplicationId' => 2, 'id' => 9, 'webServiceName' => '.....'],
        ]);

        foreach ($units as $index => $unitName) {
            $unit = AdministrativeUnit::firstOrCreate([
                'name' => $unitName
            ]);

            User::firstOrCreate([
                'username' => strtolower(str_replace(' ', '', $unitName))
            ], [
                'email' => strtolower(str_replace(' ', '', $unitName)) . '@example.com',
                'password' => Hash::make('Administrator123#'),
                'isActive' => true,
                'role' => 'administrative_unit',
                'administrativeUnitId' => $unit->id
            ]);
        }

        // Seed departments
        $defaultWebApplicationURL = null;
        $departmentsToAdd = [
            ['administrativeUnitId' => 1, 'departmentName' => 'Taxe și impozite Valea Crișului', 'webApplicationId' => $globImpApp->id, 'webApplicationURL' => 'http://globinfo.ro:7017'],
            ['administrativeUnitId' => 1, 'departmentName' => 'Registrul Agricol Valea Crișului', 'webApplicationId' => $regAgrApp->id, 'webApplicationURL' => $defaultWebApplicationURL],
            ['administrativeUnitId' => 2, 'departmentName' => 'Taxe și impozite Bodoc', 'webApplicationId' => $globImpApp->id, 'webApplicationURL' => 'http://globinfo.ro:7018'],
            ['administrativeUnitId' => 2, 'departmentName' => 'Registrul Agricol Bodoc', 'webApplicationId' => $regAgrApp->id, 'webApplicationURL' => $defaultWebApplicationURL],
            ['administrativeUnitId' => 3, 'departmentName' => 'Taxe și impozite Dalnic', 'webApplicationId' => $globImpApp->id, 'webApplicationURL' => 'http://globinfo.ro:7019'],
            ['administrativeUnitId' => 3, 'departmentName' => 'Registrul Agricol Dalnic', 'webApplicationId' => $regAgrApp->id, 'webApplicationURL' => $defaultWebApplicationURL],
            ['administrativeUnitId' => 4, 'departmentName' => 'Taxe și impozite Ilieni', 'webApplicationId' => $globImpApp->id, 'webApplicationURL' => 'http://globinfo.ro:7020'],
            ['administrativeUnitId' => 5, 'departmentName' => 'Birou social', 'webApplicationId' => null]
        ];

        foreach ($departmentsToAdd as $dept) {
            AdministrativeUnitDepartment::firstOrCreate([
                'administrativeUnitId' => $dept['administrativeUnitId'],
                'departmentName' => $dept['departmentName'],
                'webApplicationId' => $dept['webApplicationId'],
                'webApplicationURL' => $dept['webApplicationURL'] ?? $defaultWebApplicationURL
            ]);
        }


        // Seed services
        $services = [
            ['id' => 1, 'departmentId' => 1, 'serviceName' => 'Certificat fiscal', 'serviceDescription' => 'Eliberare certificat fiscal'],
            ['id' => 2, 'departmentId' => 1, 'serviceName' => 'Declarare imobil', 'serviceDescription' => 'Depunere declaratie'],
            ['id' => 3, 'departmentId' => 2, 'serviceName' => 'Modificare date gospodarie', 'serviceDescription' => 'Actualizare date gospodărie'],
            ['id' => 4, 'departmentId' => 2, 'serviceName' => 'Eliberare adeverinta APIA', 'serviceDescription' => 'adeverinta APIA ....'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate([
                'id' => $service['id']
            ], [
                'departmentId' => $service['departmentId'],
                'serviceName' => $service['serviceName'],
                'serviceDescription' => $service['serviceDescription'],
                'createDate' => now(),
                'updateDate' => now()
            ]);
        }

        // Categories seeding
        $categories = [
            ['id' => 1, 'name' => 'Știri și noutăți'],
            ['id' => 2, 'name' => 'Taxe și impozite'],
            ['id' => 3, 'name' => 'Căsătorie']
        ];

        foreach ($units as $unitName) {
            $unit = AdministrativeUnit::where('name', $unitName)->first();
            $hasTaxesDepartment = AdministrativeUnitDepartment::where('administrativeUnitId', $unit->id)
                ->where('departmentName', 'like', '%Taxe și impozite%')
                ->exists();

            foreach ($categories as $category) {
                if ($hasTaxesDepartment || $category['id'] !== 2) {
                    AnnounceCategory::firstOrCreate([
                        'administrativeUnitId' => $unit->id,
                        'announceCategoryName' => $category['name']
                    ]);
                }
            }
        }
        // SQL dump data



        DB::table('service_windows')->insert([
            ['administrativeUnitId' => 1, 'windowName' => 'Ghiseul nr.1 Taxe și Impozite', 'windowLocation' => 'str. ....'],
            ['administrativeUnitId' => 1, 'windowName' => 'Ghiseul nr.2 Registrul Agricol', 'windowLocation' => 'str. nr....'],
            ['administrativeUnitId' => 2, 'windowName' => 'Ghiseul nr.1 Taxe și Impozite', 'windowLocation' => '....'],
        ]);

        DB::table('service_window_services')->insert([
            ['serviceId' => 1, 'serviceWindowId' => 1],
            ['serviceId' => 1, 'serviceWindowId' => 2],
            ['serviceId' => 2, 'serviceWindowId' => 3],
        ]);

    }
}
