
  document.addEventListener("DOMContentLoaded", function () {
    const toDateAdd = document.getElementById("toDateAdd");
    const unlimitedAdd = document.getElementById("unlimitedCheckboxAdd");

    const toDateEdit = document.getElementById("toDateEdit");
    const unlimitedEdit = document.getElementById("unlimitedCheckboxEdit");

    // Handle ADD modal behavior
    unlimitedAdd.addEventListener("change", function () {
      if (this.checked) {
        toDateAdd.disabled = true;
        toDateAdd.value = "";
      } else {
        toDateAdd.disabled = false;
      }
    });

    // Handle EDIT modal behavior
    unlimitedEdit.addEventListener("change", function () {
      if (this.checked) {
        toDateEdit.disabled = true;
        toDateEdit.required = false;
        toDateEdit.value = "";
      } else {
        toDateEdit.disabled = false;
        toDateEdit.required = true;
      }
    });

    // Optional: set initial state when modals open
    function checkInitialState() {
      if (unlimitedAdd.checked) toDateAdd.disabled = true;
      if (unlimitedEdit.checked) {
        toDateEdit.disabled = true;
        toDateEdit.required = false;
      }
    }

    checkInitialState(); // Run once in case modals are pre-filled
  });
