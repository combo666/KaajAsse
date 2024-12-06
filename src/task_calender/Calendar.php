<?php
class Calendar
{
    private $active_year, $active_month, $active_day;
    private $events = [];

    public function __construct($date = null)
    {
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

    public function add_event($txt, $date, $days = 1, $color = '')
    {
        // Only add event if user role is allowed (role check happens in task_calendar.php)
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'a') {
            return; // Prevent unauthorized users from adding events
        }

        $color = $color ? ' ' . $color : $color;
        $this->events[] = [$txt, $date, $days, $color];
    }

    public function __toString()
{
    $num_days = date('t', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day));
    $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
    $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);

    $html = '<div class="calendar">';
    $html .= '<div class="header">';
    $html .= '<div class="month-year">';
    $html .= date('F Y', strtotime($this->active_year . '-' . $this->active_month)) . '</div>';
    $html .= '</div>';

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
        $html .= '<div class="day_num" onclick="openPopup(\'' . $formatted_date . '\')">';
        $html .= '<span>' . $i . '</span>';

        // Add events for the current day
        foreach ($this->events as $event) {
            for ($d = 0; $d <= ($event[2] - 1); $d++) {
                $event_date = date('Y-m-d', strtotime($event[1] . ' +' . $d . ' day'));
                if ($formatted_date === $event_date) {
                    $color_style = $event[3] ? 'style="background-color:' . htmlspecialchars($event[3]) . ';"' : '';
                    $html .= '<div class="event" ' . $color_style . '>' . htmlspecialchars($event[0]) . '</div>';
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
