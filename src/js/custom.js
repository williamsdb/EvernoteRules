function confirmRedirect(url) {
  if (confirm("Are you sure you want to proceed?")) {
    window.location.href = url;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("year").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    let input = document.getElementById("tagText");
    let appendText = "{year}";

    // Update input field
    input.value = input.value.trim();
    if (input.value === "") {
      input.value = appendText;
    } else {
      input.value = input.value.trim() + ", " + appendText;
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("month").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    let input = document.getElementById("tagText");
    let appendText = "{month}";

    // Update input field
    input.value = input.value.trim();
    if (input.value === "") {
      input.value = appendText;
    } else {
      input.value = input.value.trim() + ", " + appendText;
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("day").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    let input = document.getElementById("tagText");
    let appendText = "{day}";

    // Update input field
    input.value = input.value.trim();
    if (input.value === "") {
      input.value = appendText;
    } else {
      input.value = input.value.trim() + ", " + appendText;
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("dayord").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    let input = document.getElementById("tagText");
    let appendText = "{dayord}";

    // Update input field
    input.value = input.value.trim();
    if (input.value === "") {
      input.value = appendText;
    } else {
      input.value = input.value.trim() + ", " + appendText;
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("dow").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    let input = document.getElementById("tagText");
    let appendText = "{dow}";

    // Update input field
    input.value = input.value.trim();
    if (input.value === "") {
      input.value = appendText;
    } else {
      input.value = input.value.trim() + ", " + appendText;
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("date").addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default anchor behavior

    let input = document.getElementById("tagText");
    let appendText = "{date}";

    // Update input field
    input.value = input.value.trim();
    if (input.value === "") {
      input.value = appendText;
    } else {
      input.value = input.value.trim() + ", " + appendText;
    }
  });
});
