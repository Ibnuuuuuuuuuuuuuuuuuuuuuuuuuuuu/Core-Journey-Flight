<?php

namespace Database\Seeders;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\FlightSchedule;
use App\Models\FlightSeatClass;
use App\Models\Route;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        $cgk = Airport::query()->firstOrCreate([
            'airport_code' => 'CGK',
        ], [
            'airport_name' => 'Soekarno-Hatta International Airport',
            'city_name' => 'Jakarta',
            'country_name' => 'Indonesia',
        ]);

        $dps = Airport::query()->firstOrCreate([
            'airport_code' => 'DPS',
        ], [
            'airport_name' => 'I Gusti Ngurah Rai International Airport',
            'city_name' => 'Denpasar',
            'country_name' => 'Indonesia',
        ]);

        $sub = Airport::query()->firstOrCreate([
            'airport_code' => 'SUB',
        ], [
            'airport_name' => 'Juanda International Airport',
            'city_name' => 'Surabaya',
            'country_name' => 'Indonesia',
        ]);

        $airlines = [
            Airline::query()->firstOrCreate(['airline_code' => 'GA'], ['airline_name' => 'Garuda Indonesia']),
            Airline::query()->firstOrCreate(['airline_code' => 'QZ'], ['airline_name' => 'AirAsia Indonesia']),
            Airline::query()->firstOrCreate(['airline_code' => 'ID'], ['airline_name' => 'Batik Air']),
            Airline::query()->firstOrCreate(['airline_code' => 'QG'], ['airline_name' => 'Citilink Indonesia']),
            Airline::query()->firstOrCreate(['airline_code' => 'JT'], ['airline_name' => 'Lion Air']),
        ];

        $routes = [
            'CGK-SUB' => Route::query()->firstOrCreate([
                'route_code' => 'CGK-SUB',
            ], [
                'origin_id' => $cgk->id,
                'destination_id' => $sub->id,
                'distance_km' => 690,
            ]),
            'CGK-DPS' => Route::query()->firstOrCreate([
                'route_code' => 'CGK-DPS',
            ], [
                'origin_id' => $cgk->id,
                'destination_id' => $dps->id,
                'distance_km' => 980,
            ]),
            'SUB-CGK' => Route::query()->firstOrCreate([
                'route_code' => 'SUB-CGK',
            ], [
                'origin_id' => $sub->id,
                'destination_id' => $cgk->id,
                'distance_km' => 690,
            ]),
            'SUB-DPS' => Route::query()->firstOrCreate([
                'route_code' => 'SUB-DPS',
            ], [
                'origin_id' => $sub->id,
                'destination_id' => $dps->id,
                'distance_km' => 410,
            ]),
            'DPS-CGK' => Route::query()->firstOrCreate([
                'route_code' => 'DPS-CGK',
            ], [
                'origin_id' => $dps->id,
                'destination_id' => $cgk->id,
                'distance_km' => 980,
            ]),
            'DPS-SUB' => Route::query()->firstOrCreate([
                'route_code' => 'DPS-SUB',
            ], [
                'origin_id' => $dps->id,
                'destination_id' => $sub->id,
                'distance_km' => 410,
            ]),
        ];

        $routeConfigs = [
            'CGK-SUB' => [
                'origin_code' => $cgk->airport_code,
                'destination_code' => $sub->airport_code,
                'flight_prefix' => 'CQ',
                'base_price' => 760000,
                'duration_minutes' => 95,
                'departure_times' => ['06:00', '07:15', '08:30', '09:45', '11:00', '12:15', '14:00', '15:20', '17:05', '19:10'],
            ],
            'CGK-DPS' => [
                'origin_code' => $cgk->airport_code,
                'destination_code' => $dps->airport_code,
                'flight_prefix' => 'GA',
                'base_price' => 1680000,
                'duration_minutes' => 155,
                'departure_times' => ['06:10', '07:40', '09:05', '10:50', '12:25', '14:10', '15:55', '17:35', '19:20', '21:00'],
            ],
            'SUB-CGK' => [
                'origin_code' => $sub->airport_code,
                'destination_code' => $cgk->airport_code,
                'flight_prefix' => 'ID',
                'base_price' => 740000,
                'duration_minutes' => 95,
                'departure_times' => ['06:20', '07:35', '08:50', '10:05', '11:20', '13:00', '14:25', '16:10', '18:00', '20:15'],
            ],
            'SUB-DPS' => [
                'origin_code' => $sub->airport_code,
                'destination_code' => $dps->airport_code,
                'flight_prefix' => 'QG',
                'base_price' => 940000,
                'duration_minutes' => 80,
                'departure_times' => ['06:30', '07:50', '09:10', '10:30', '12:00', '13:35', '15:05', '16:40', '18:20', '20:05'],
            ],
            'DPS-CGK' => [
                'origin_code' => $dps->airport_code,
                'destination_code' => $cgk->airport_code,
                'flight_prefix' => 'JT',
                'base_price' => 1710000,
                'duration_minutes' => 155,
                'departure_times' => ['06:00', '07:30', '09:00', '10:35', '12:10', '13:45', '15:25', '17:00', '18:40', '20:20'],
            ],
            'DPS-SUB' => [
                'origin_code' => $dps->airport_code,
                'destination_code' => $sub->airport_code,
                'flight_prefix' => 'QZ',
                'base_price' => 980000,
                'duration_minutes' => 80,
                'departure_times' => ['06:15', '07:45', '09:15', '10:45', '12:20', '13:55', '15:30', '17:05', '18:45', '20:30'],
            ],
        ];

        $statusCycle = ['scheduled', 'scheduled', 'boarding', 'scheduled', 'departed'];
        $baseDate = Carbon::parse('2026-04-19');
        $departureSlots = [
            ['time' => '06:00', 'duration' => 95],
            ['time' => '10:30', 'duration' => 95],
            ['time' => '18:45', 'duration' => 95],
        ];
        $dateCount = 3;

        foreach ($routeConfigs as $routeCode => $config) {
            $route = $routes[$routeCode];
            for ($dayIndex = 0; $dayIndex < $dateCount; $dayIndex++) {
                $flightDate = $baseDate->copy()->addDays($dayIndex);

                foreach ($departureSlots as $slotIndex => $slot) {
                    $airline = $airlines[($dayIndex + $slotIndex + strlen($routeCode)) % count($airlines)];
                    $numericCode = 500 + ($dayIndex * 60) + ($slotIndex * 10) + strlen($routeCode);
                    $flightNumber = sprintf('%s %03d', $airline->airline_code, $numericCode);
                    $departureAt = Carbon::createFromFormat('Y-m-d H:i', $flightDate->toDateString() . ' ' . $slot['time']);
                    $arrivalAt = $departureAt->copy()->addMinutes($config['duration_minutes']);
                    $basePrice = $config['base_price'] + ($dayIndex * 45000) + ($slotIndex * 25000) + (str_starts_with($routeCode, 'CGK-DPS') ? 70000 : 0);
                    $status = $statusCycle[($dayIndex + $slotIndex) % count($statusCycle)];

                    $schedule = FlightSchedule::query()->updateOrCreate(
                        [
                            'flight_number' => $flightNumber,
                            'route_id' => $route->id,
                            'departure_date' => $flightDate->toDateString(),
                            'departure_time' => $departureAt->toTimeString(),
                        ],
                        [
                            'airline_id' => $airline->id,
                            'origin' => $config['origin_code'],
                            'destination' => $config['destination_code'],
                            'arrival_time' => $arrivalAt->toTimeString(),
                            'base_price' => $basePrice,
                            'flight_status' => $status,
                        ]
                    );

                    $classDefinitions = [
                        'economy' => [
                            'seat_capacity' => 180,
                            'available_seats' => max(40, 180 - ($dayIndex * 12) - ($slotIndex * 5)),
                            'class_price' => $basePrice,
                        ],
                        'business' => [
                            'seat_capacity' => 24,
                            'available_seats' => max(4, 24 - ($dayIndex * 2) - $slotIndex),
                            'class_price' => (int) round($basePrice * 2.65),
                        ],
                        'first_class' => [
                            'seat_capacity' => 8,
                            'available_seats' => max(2, 8 - $dayIndex),
                            'class_price' => (int) round($basePrice * 4.1),
                        ],
                    ];

                    foreach ($classDefinitions as $seatClass => $classConfig) {
                        FlightSeatClass::query()->updateOrCreate(
                            [
                                'flight_schedule_id' => $schedule->id,
                                'seat_class' => $seatClass,
                            ],
                            $classConfig
                        );
                    }
                }
            }
        }
    }
}
