<!DOCTYPE html>
<html>
<head>
    <title>Alert with Input, Confirm, and Cancel</title>
</head>
<body>
    <button onclick="showPrompt()">Click me</button>

    <script>
        function showPrompt() {
            const input = prompt("Please enter your name:");
            if (input !== null && input !== "") {
                const confirmation = confirm("You entered: " + input + ". Is this correct?");
                if (confirmation) {
                    alert("Thank you! Your input has been confirmed.");
                } else {
                    alert("Input was not confirmed. Please try again.");
                }
            } else {
                alert("No input provided.");
            }
        }
    </script>
</body>
</html>