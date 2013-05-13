function Console () {
}

// To turn off all console features, set Console.enabled to false.
Console.enabled = true;

// Console.log(object[, object, ...]) - Print a message in the log. If
// FireBug is enabled, print the message in the FireBug
// console. Otherwise, do nothing.
Console.log = function () {
    if (Console.enabled && window.console && window.console.log) {
        console.log.apply(console, arguments);
    }
};

// Console.debug(object[, object, ...]) - Writes a message to the
// FireBug console, including a hyperlink to the line where it was
// called.
Console.debug = function () {
    if (Console.enabled && window.console && window.console.debug) {
        console.debug.apply(console, arguments);
    }
};

// Console.info(object[, object, ...]) - Writes a message to the
// console with the visual "info" icon and color coding and a
// hyperlink to the line where it was called.
Console.info = function () {
    if (Console.enabled && window.console && window.console.info) {
        console.info.apply(console, arguments);
    }
};

// Console.warn(object[, object, ...]) - Writes a message to the
// console with the visual "warning" icon and color coding and a
// hyperlink to the line where it was called.
Console.warn = function () {
    if (Console.enabled && window.console && window.console.warn) {
        console.warn.apply(console, arguments);
    }
};

// Console.error(object[, object, ...]) - Writes a message to the
// console with the visual "error" icon and color coding and a
// hyperlink to the line where it was called. If FireBug is not
// enabled, an alert box is displayed, requesting that the user enable
// FireBug to debug the problem.
Console.error = function () {
    if (Console.enabled && window.console && window.console.error) {
        console.error.apply(console, arguments);
    } else if (Console.enabled) {
        var errMsg = "";
        for (arg in arguments) {
            errMsg += arg;
        }
        alert("An error has occurred. Please enable FireBug to debug it. " +
            errMsg);
    }
};

// Console.assert(expression[, object, ...]) - Tests that an
// expression is true. If not, it will write a message to the console
// and throw an exception.
Console.assert = function () {
    if (Console.enabled && window.console && window.console.assert) {
        console.assert.apply(console, arguments);
    } else if (Console.enabled && arguments[0] == false) {
        alert("An error has occurred. Please enable FireBug to debug it. " +
            arguments[1]);
    }
};

// Console.dir(object) - Prints an interactive listing of all
// properties of the object. This looks identical to the view that you
// would see in the DOM tab.
Console.dir = function () {
    if (Console.enabled && window.console && window.console.dir) {
        console.dir.apply(console, arguments);
    }
};