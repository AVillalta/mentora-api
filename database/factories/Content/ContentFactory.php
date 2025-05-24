<?php

namespace Database\Factories\Content;

use App\Models\Course\Course;
use App\Models\Content\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['document', 'presentation', 'code']);
        $format = $this->getFormatForType($type);

        return [
            'name' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(2),
            'bibliography' => $this->faker->optional(0.5)->paragraph(),
            'order' => $this->faker->numberBetween(1, 100),
            'type' => $type,
            'format' => $format,
            'views' => $this->faker->numberBetween(0, 100),
            'downloads' => $this->faker->numberBetween(0, 50),
            'duration' => null,
            'course_id' => Course::inRandomOrder()->first()->id ?? Course::factory()->create()->id,
        ];
    }

    /**
     * Get the appropriate format for the given content type.
     *
     * @param string $type
     * @return string
     */
    public function getFormatForType(string $type): string
    {
        $formats = [
            'document' => ['pdf'], // Eliminado 'docx'
            'presentation' => ['pdf'],
            'code' => ['txt'],
        ];

        return $this->faker->randomElement($formats[$type]);
    }

    /**
     * Get a reliable external URL for the given content type and format.
     *
     * @param string $type
     * @param string $format
     * @return string
     */
    public function getFileUrl(string $type, string $format): string
    {
        $fileUrls = [
            'pdf' => [
                'https://www.irs.gov/pub/irs-pdf/n1036.pdf', // ~200 KB
            ],
            'txt' => [
                'https://www.gutenberg.org/files/1342/1342-0.txt', // ~700 KB
            ],
        ];

        return $this->faker->randomElement($fileUrls[$format] ?? ['https://www.irs.gov/pub/irs-pdf/n1036.pdf']);
    }
}