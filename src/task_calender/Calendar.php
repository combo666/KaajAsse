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
        $color = $color ? ' ' . $color : $color;
        $this->events[] = [$txt, $date, $days, $color];
    }

    public function __toString()
    {
        $num_days = date('t', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year));
        $num_days_last_month = date('j', strtotime('last day of previous month', strtotime($this->active_day . '-' . $this->active_month . '-' . $this->active_year)));
        $days = [0 => 'Sun', 1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat'];
        $first_day_of_week = array_search(date('D', strtotime($this->active_year . '-' . $this->active_month . '-1')), $days);
        $html = '<div class="calendar">';
        $html .= '<div class="header">';
        $html .= '<div class="month-year" onmouseover="showDropdown()" onmouseout="hideDropdown()">';
        $html .= '<span id="month-year-label">' . date('F Y, d', strtotime($this->active_year . '-' . $this->active_month . '-' . $this->active_day)) . '</span>';
        $html .= '<div id="month-year-dropdown" style="display: none;">';
        $html .= '<select id="month-select" onchange="updateCalendar()">';
        // Add months to the dropdown
        for ($m = 1; $m <= 12; $m++) {
            $selected = $m == $this->active_month ? 'selected' : '';
            $html .= '<option value="' . $m . '" ' . $selected . '>' . date('F', mktime(0, 0, 0, $m, 10)) . '</option>';
        }
        $html .= '</select>';
        $html .= '<select id="year-select" onchange="updateCalendar()">';
        // Generate a range of years for selection
        for ($y = $this->active_year - 10; $y <= $this->active_year + 10; $y++) {
            $selected = $y == $this->active_year ? 'selected' : '';
            $html .= '<option value="' . $y . '" ' . $selected . '>' . $y . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '<div class="days">';
        foreach ($days as $day) {
            $html .= '
                <div class="day_name">
                    ' . $day . '
                </div>
            ';
        }
        for ($i = $first_day_of_week; $i > 0; $i--) {
            $html .= '
                <div class="day_num ignore">
                    ' . ($num_days_last_month - $i + 1) . '
                </div>
            ';
        }
        for ($i = 1; $i <= $num_days; $i++) {
            $selected = ($i == $this->active_day) ? ' selected' : '';
            $formattedDate = $this->active_year . '-' . str_pad($this->active_month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);

            $html .= '<div class="day_num' . $selected . '" onclick="openPopup(\'' . $formattedDate . '\')">';
            $html .= '<span>' . $i . '</span>';

            // Add events for the day
            foreach ($this->events as $event) {
                for ($d = 0; $d <= ($event[2] - 1); $d++) {
                    if (date('Y-m-d', strtotime($formattedDate . ' -' . $d . ' day')) == date('Y-m-d', strtotime($event[1]))) {
                        $html .= '<div class="event' . $event[3] . '">';
                        $html .= $event[0];
                        $html .= '</div>';
                    }
                }
            }
            $html .= '</div>';
        }

        for ($i = 1; $i <= (42 - $num_days - max($first_day_of_week, 0)); $i++) {
            $html .= '
                <div class="day_num ignore">
                    ' . $i . '
                </div>
            ';
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
