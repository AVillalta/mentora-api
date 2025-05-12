<?php

namespace App\Data\Semester;

use Spatie\LaravelData\Data;

class SemesterCalendarData extends Data{

    public function __construct(
        public array $holidays = [],
        public array $vacations = [],
    ){}

    /**
     * Create a CalendarDTO instance from an associative array.
     *
     * @param array $data An array containing 'holidays' and 'vacations' keys.
     * @return self A new instance of CalendarDTO.
     */
    public static function fromArray(array $data) : self 
    {
        return new self(
            holidays: $data['holidays'] ?? [],
            vacations: $data['vacations'] ?? [],
        );
    }

      /**
     * Convert the CalendarDTO back to an associative array.
     *
     * @return array An array representation of the calendar data.
     */
    public function toArray() : array 
    {
        return [
            'holidays' => $this->holidays,
            'vacations' => $this->vacations,
        ];    
    }

    /**
     * Add a holiday to the calendar.
     *
     * @param string $date The date of the holiday.
     * @param string $description A description of the holiday.
     * @return self Returns the instance for method chaining.
     */
    public function addHoliday(string $date, string $description) : self
    {
        $this->holidays[$date] = $description;
        return $this;    
    }

     /**
     * Add a vacation period to the calendar.
     *
     * @param array $period An array representing a vacation period with keys like 'start', 'end', and 'description'.
     * @return self Returns the instance for method chaining.
     */
    public function addVacationPeriod(array $period) : self 
    {
        $this->vacations[] = $period;
        return $this;    
    }

}