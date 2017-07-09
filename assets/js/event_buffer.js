/**
 * This Function buffers the events fired by frequently executed event listeners
 *
 * @param function fn Callback function
 * @param int delay The desired Delay
 * @param bool continuous If true the buffered event is only fired all {$delay}ms
 *                        If false the event is only fired after the last event was fired {$delay}ms ago
 */
var evt_buffer = (function() {
  return function(fn, delay, continuous) {
    delay      = delay      || 200;
    continuous = continuous || false;

    var timer     = null,
        last_args = null,
        last_ctxt = null;

    // Only allow one event each "$delay" ms
    var fn_continuous = function() {
      var current_arguments = arguments,
          current_context = this;

      //(in parent scope) is overwritten by following evt-call
      last_args = arguments;
      last_ctxt = this;

      if(timer !== null)
        return;

      timer = setTimeout(function() {
        fn.apply(current_context, current_arguments);
        if(last_args === current_arguments && last_ctxt === current_context) {
          timer = null;
          return;
        }

        current_arguments = last_args;
        current_context = last_ctxt;
        timer = setTimeout(arguments.callee, delay);

      }, delay);
    };

    // Only one event after "$delay" time is over
    var fn_deferred = function() {
      if(timer) {
        clearTimeout(timer);
        timer = null;
      }

      var args = arguments;
      var context = this;
      timer = setTimeout(function() {
        fn.apply(context, args);
      }, delay);
    };

    return continuous === true ? fn_continuous : fn_deferred;
  };
})();