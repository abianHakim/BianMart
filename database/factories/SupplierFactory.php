<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'nama_supplier' => $this->faker->randomElement([
                'PT Indofood Sukses Makmur Tbk',
                'PT Mayora Indah Tbk',
                'PT Unilever Indonesia Tbk',
                'PT Nestle Indonesia',
                'PT Frisian Flag Indonesia',
                'PT Danone Indonesia',
                'PT Yakult Indonesia Persada',
                'PT Sinar Mas Agro Resources and Technology Tbk',
                'PT Salim Ivomas Pratama Tbk',
                'PT Japfa Comfeed Indonesia Tbk',
            ]),
            'telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),

        ];
    }
}
