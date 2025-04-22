// confirmation on clicking delete link
function confirmRedirect(url) {
  if (confirm("Are you sure you want to proceed?")) {
    window.location.href = url;
  }
}

// handler for the spintax shortcodes on add/edit action
document.addEventListener("DOMContentLoaded", function () {
  let buttons = [
    { id: "year", text: "{year}" },
    { id: "month", text: "{month}" },
    { id: "day", text: "{day}" },
    { id: "dayord", text: "{dayord}" },
    { id: "dow", text: "{dow}" },
    { id: "date", text: "{date}" },
  ];

  buttons.forEach(function (button) {
    let element = document.getElementById(button.id);
    if (element) {
      element.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default anchor behavior

        let input = document.getElementById("tagText");
        let appendText = button.text;

        // Update input field
        input.value = input.value.trim();
        if (input.value === "") {
          input.value = appendText;
        } else {
          input.value = input.value.trim() + ", " + appendText;
        }
      });
    }
  });
});

$(document).ready(function () {
  // setup js required for datatables
  $("#logTable").DataTable({
    columns: [
      { targets: 0, visible: true, className: "never" },
      { targets: 1, orderData: 0 },
      { targets: 2, orderData: 0 },
    ],
    order: [[0, "desc"]],
    pageLength: 10,
    responsive: true,
    paging: true,
    ordering: true,
    searching: true,
    info: true,
  });

  var table = $("#logTable").DataTable();

  table.on("page.dt", function () {
    $("html, body").animate(
      {
        scrollTop: $("#logTable").offset().top,
      },
      300
    ); // Adjust speed as needed
  });

  $("#rulesTable").DataTable({
    columns: [
      { targets: 0, visible: true },
      { targets: 1, visible: true },
      { targets: 2, visible: true },
      { targets: 3, visible: true },
    ],
    order: [[0, "asc"]],
    pageLength: 10,
    responsive: true,
    paging: true,
    ordering: true,
    searching: true,
    info: true,
  });

  var rulesTable = $("#rulesTable").DataTable();

  rulesTable.on("page.dt", function () {
    $("html, body").animate(
      {
        scrollTop: $("#rulesTable").offset().top,
      },
      300
    ); // Adjust speed as needed
  });

  // setup js required for sortable table
  let sortableElement = document.querySelector("#sortable-actions tbody");
  if (sortableElement) {
    new Sortable(sortableElement, {
      animation: 150,
      handle: ".drag-handle",
      onEnd: function (evt) {
        console.log("Moved row from index", evt.oldIndex, "to", evt.newIndex);
        const oldIndex = evt.oldIndex;
        const newIndex = evt.newIndex;

        // Send oldIndex and newIndex to backend via fetch (or jQuery.ajax)
        fetch("/updateOrder.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            ruleId: document.getElementById("ruleId").value,
            oldIndex: oldIndex,
            newIndex: newIndex,
          }),
        })
          .then((response) => response.text()) // ← get raw text for debug
          .then((text) => {
            console.log("Raw response:", text);
            const data = JSON.parse(text); // now you see what went wrong
            if (!data.success) {
              alert("Error updating order");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
          });
      },
    });
  }
});
