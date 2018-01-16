!function($) {
    var $countdown = $('.input-datepicker'),
        $timedown = $('.input-timepicker');
    if($countdown.size() > 0) {
        $countdown.each(function () {
            var $this = $(this),
                mask = $this.data('mask');

            $this.datepicker({
                dateFormat : mask
            });

        });
    }
    if($timedown.size() > 0) {
        $timedown.each(function () {
            var $this = $(this),
                mask = $this.data('mask');

            $this.timepicker({
                timeFormat : mask
            });

        });
    }

}(window.jQuery);