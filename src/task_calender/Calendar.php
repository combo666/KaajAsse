<?php

class Calendar
{
    private $active_year, $active_month, $active_day;
    private $events = [];


    public function __construct($date = null)
    {
        date_default_timezone_set('Asia/Dhaka');
        if (isset($_GET['month']) && isset($_GET['year'])) {
            $this->active_month = $_GET['month'];
            $this->active_year = $_GET['year'];
            $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
        } else {
            $this->active_year = $date != null ? date('Y', strtotime($date)) : date('Y');
            $this->active_month = $date != null ? date('m', strtotime($date)) : date('m');
            $this->active_day = $date != null ? date('d', strtotime($date)) : date('d');
        }
    }

    public function add_event($txt, $date, $days = 1, $color = '', $assigned_user = null)
    {
        $color = $color ? ' ' . $color : $color;
        $this->events[] = [
            'text' => $txt,
            'date' => $date,
            'days' => $days,
            'color' => $color,
            'assigned_user' => $assigned_user,
        ];
    }

    public function __toString()
    {
        $num_days = date('t', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day));
        $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
        $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);

        $html = '<div class="calendar">';
        $html .= '<div class="header">';
        $html .= '<div class="month-year" id="month-year">';
        $html .= date('F Y', strtotime($this->active_year . '-' . $this->active_month));

        $html .= '<div style="display: inline-flex; align-items: center;">';

        $html .= '<div id="month-year" style="">';

        // Month dropdown
        $html .= '<select id="month-select" onchange="updateCalendar()" style="margin-right: 10px; margin-left: 10px;">';
        for ($m = 1; $m <= 12; $m++) {
            $selected = ($m == $this->active_month) ? 'selected' : '';
            $html .= '<option value="' . $m . '" ' . $selected . '>' . date('F', mktime(0, 0, 0, $m, 10)) . '</option>';
        }
        $html .= '</select>';

        // Year dropdown
        $html .= '<select id="year-select" onchange="updateCalendar()" style="margin-left: 10px;">'; // Add margin-left here
        for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++) {
            $selected = ($y == $this->active_year) ? 'selected' : '';
            $html .= '<option value="' . $y . '" ' . $selected . '>' . $y . '</option>';
        }
        $html .= '</select>';

        $html .= '</div>'; // Close month-year-dropdown
        $html .= '</div>';

        $html .= '</div>'; // Close month-year
        $html .= '</div>'; // Close header

        $html .= '<div class="days">';
        foreach ($days as $day) {
            $html .= '<div class="day_name">' . $day . '</div>';
        }

        // Add days from the previous month
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_year . '-' . $this->active_month)));
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '<div class="day_num ignore">' . ($num_days_last_month - $i + 1) . '</div>';
        }

        // Add days for the current month
        for ($i = 1; $i <= $num_days; $i++) {
            $formatted_date = $this->active_year . '-' . str_pad($this->active_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);

            // Check if the current day is the same as the active day
            $is_today = ($formatted_date === date('Y-m-d')) ? 'selected' : '';

            $html .= '<div class="day_num ' . $is_today . '" onclick="openPopup(\'' . $formatted_date . '\')">';
            $html .= '<span>' . $i . '</span>';

            // Add events for the current day
            foreach ($this->events as $event) {
                for ($d = 0; $d <= ($event['days'] - 1); $d++) {
                    $event_date = date('Y-m-d', strtotime($event['date'] . ' +' . $d . ' day'));

                    // Display all events regardless of roles or assignments
                    if ($formatted_date === $event_date) {
                        $color_style = $event['color'] ? 'style="background-color:' . htmlspecialchars($event['color']) . ';"' : '';
                        $html .= '<div class="event" ' . $color_style . '>' . htmlspecialchars($event['text']) . '</div>';
                    }
                }
            }

            $html .= '</div>';
        }

        // Add extra empty days for the next month
        $total_days_displayed = $first_day_of_week + $num_days;
        $extra_days = 42 - $total_days_displayed;
        for ($i = 1; $i <= $extra_days; $i++) {
            $html .= '<div class="day_num ignore">' . $i . '</div>';
        }

        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
