function validateForm(event) {
    var description = document.getElementById("description").value.trim();
    var amount = document.getElementById("amount").value.trim();
    var type = document.getElementById("type").value;

    if (description === "") {
        alert("Please enter a description.");
        event.preventDefault();
        return false;
    }

    if (amount === "" || isNaN(amount) || parseFloat(amount) <= 0) {
        alert("Please enter a valid amount.");
        event.preventDefault();
        return false;
    }

    if (type === "") {
        alert("Please select a transaction type (Income or Expense).");
        event.preventDefault();
        return false;
    }

    return true;
}