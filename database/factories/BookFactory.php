<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        $total = $this->faker->numberBetween(1, 5);

        return [
            'title'            => $this->faker->sentence(4),
            'author'           => $this->faker->name(),
            'isbn'             => $this->faker->isbn13(),
            'category'         => $this->faker->randomElement(['Roman', 'Sciences', 'Histoire', 'Informatique', 'Philosophie']),
            'description'      => $this->faker->paragraph(),
            'total_copies'     => $total,
            'available_copies' => $total,
        ];
    }

    /**
     * État : livre entièrement emprunté.
     */
    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_copies' => 0,
        ]);
    }
}
