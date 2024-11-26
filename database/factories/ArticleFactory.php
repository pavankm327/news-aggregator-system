<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // return [
        //     'title' => fake()->sentence,
        //     'description' => fake()->paragraph,
        //     'author' => fake()->name,
        //     'source' => fake()->company,
        //     'category' => fake()->word,
        //     'published_at' => fake()->date,
        // ];

        return [
            'title' => fake()->sentence(6, true), // Generates a title with up to 6 words
            'description' => fake()->paragraph(8, true), // Generates a paragraph with 8 sentences
            'author' => fake()->name(), // Generates a realistic name
            'source' => fake()->company(), // Generates a company name as the source
            'category' => fake()->randomElement(['Technology', 'Health', 'Education', 'Business', 'Sports']), // Generates a meaningful category
            'published_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'), // Date within the past year
        ];

        // return [
        //     'title' => fake()->realText(50), // Generates a realistic sentence up to 50 characters
        //     'description' => fake()->realText(200), // Generates a paragraph up to 200 characters
        //     'author' => fake()->name(), // Generates a realistic name
        //     'source' => fake()->company(), // Generates a company name as the source
        //     'category' => fake()->randomElement(['Technology', 'Health', 'Education', 'Business', 'Sports']), // Generates a meaningful category
        //     'published_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'), // Date within the past year
        // ];

        // return [
        //     'title' => fake()->randomElement([
        //         'Advancements in Artificial Intelligence Transform Industries',
        //         'The Rise of Renewable Energy in the Modern Era',
        //         'Breakthroughs in Healthcare Technology for 2024',
        //         'The Global Shift Towards Remote Work Practices',
        //         'Exploring the Intersection of Technology and Education',
        //     ]), // Predefined real titles for better realism

        //     'description' => fake()->randomElement([
        //         'Artificial intelligence is not just a trend; it is reshaping industries across the globe. From automation to advanced analytics, AI stands as a cornerstone of modern innovation.',
        //         'Renewable energy sources like solar and wind are becoming increasingly popular. This article explores their impact on economies and environmental sustainability.',
        //         'Healthcare technology is advancing rapidly, with innovations such as telemedicine, wearable devices, and personalized treatments gaining traction worldwide.',
        //         'The COVID-19 pandemic has accelerated the shift towards remote work. Companies are now leveraging technology to maintain productivity and employee engagement.',
        //         'Technology is revolutionizing education, with tools like online learning platforms, AI tutors, and virtual reality creating immersive learning experiences.',
        //     ]), // Predefined real paragraphs for better realism
        //     'author' => fake()->name(), // Generates a realistic name
        //     'source' => fake()->company(), // Generates a company name as the source
        //     'category' => fake()->randomElement(['Technology', 'Health', 'Education', 'Business', 'Sports']), // Generates a meaningful category
        //     'published_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'), // Date within the past year
        // ];
    }
}
