(function(v) {
    "use strict";
    
    function CalendarPage() {}

    CalendarPage.prototype.init = function() {
        var a = v("#event-modal"),
            t = v("#modal-title"),
            n = v("#form-event"),
            l = null,
            i = null,
            r = document.getElementsByClassName("needs-validation"),
            e = new Date(),
            s = e.getDate(),
            d = e.getMonth(),
            e = e.getFullYear();

        // Draggable external events
        new FullCalendarInteraction.Draggable(document.getElementById("external-events"), {
            itemSelector: ".external-event",
            eventData: function(e) {
                return {
                    title: e.innerText,
                    className: v(e).data("class")
                };
            }
        });

        // Predefined events
        e = [
            { title: "All Day Event", start: new Date(e, d, 1) },
            { title: "Long Event", start: new Date(e, d, s - 5), end: new Date(e, d, s - 2), className: "bg-warning" },
            { id: 999, title: "Repeating Event", start: new Date(e, d, s - 3, 16, 0), allDay: !1, className: "bg-info" },
            { id: 999, title: "Repeating Event", start: new Date(e, d, s + 4, 16, 0), allDay: !1, className: "bg-primary" },
            { title: "Meeting", start: new Date(e, d, s, 10, 30), allDay: !1, className: "bg-success" },
            { title: "Lunch", start: new Date(e, d, s, 12, 0), end: new Date(e, d, s, 14, 0), allDay: !1, className: "bg-danger" },
            { title: "Birthday Party", start: new Date(e, d, s + 1, 19, 0), end: new Date(e, d, s + 1, 22, 30), allDay: !1, className: "bg-success" },
            { title: "Click for Google", start: new Date(e, d, 28), end: new Date(e, d, 29), url: "http://google.com/", className: "bg-dark" }
        ];

        // Calendar initialization
        var d = document.getElementById("calendar");

        var c = new FullCalendar.Calendar(d, {
            plugins: ["bootstrap", "interaction", "dayGrid", "timeGrid"],
            editable: true,
            droppable: true,
            selectable: true,
            defaultView: "dayGridMonth",
            themeSystem: "bootstrap",
            header: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth"
            },
            eventClick: function(e) {
                a.modal("show"),
                n[0].reset(),
                l = e.event,
                v("#event-title").val(l.title),
                v("#event-category").val(l.classNames[0]),
                t.text("Edit Event"),
                i = null;
            },
            dateClick: function(e) {
                a.modal("show"),
                n.removeClass("was-validated"),
                n[0].reset(),
                t.text("Add Event"),
                i = e;
            },
            events: e
        });

        c.render();

        // Form submission for adding/editing events
        v(n).on("submit", function(e) {
            e.preventDefault();
            var t = v("#event-title").val(),
                e = v("#event-category").val();

            if (!r[0].checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                r[0].classList.add("was-validated");
            } else {
                if (l) {
                    l.setProp("title", t);
                    l.setProp("classNames", [e]);
                } else {
                    c.addEvent({ title: t, start: i.date, allDay: i.allDay, className: e });
                }
                a.modal("hide");
            }
        });

        // Deleting events
        v("#btn-delete-event").on("click", function(e) {
            l && (l.remove(), l = null, a.modal("hide"));
        });

        // Adding new event
        v("#btn-new-event").on("click", function(e) {
            a.modal("show"),
            n.removeClass("was-validated"),
            n[0].reset(),
            t.text("Add Event"),
            i = { date: new Date(), allDay: true };
        });
    };

    v.CalendarPage = new CalendarPage();
    v.CalendarPage.Constructor = CalendarPage;
})(window.jQuery);

// Initialize the calendar page
(function() {
    "use strict";
    window.jQuery.CalendarPage.init();
})();
