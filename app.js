document.addEventListener('DOMContentLoaded', function () {
    // Confirm before voting
    const voteForms = document.querySelectorAll('form.vote-form');
    voteForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const name = form.dataset.candidateName || 'this contestant';
            const confirmed = window.confirm(`Are you sure you want to vote for ${name}?`);
            if (!confirmed) {
                event.preventDefault();
            }
        });
    });

    // Enable button only when all required fields are filled
    const authForms = document.querySelectorAll('form.validation-form');
    authForms.forEach(function (form) {
        const submitButton = form.querySelector('button[type="submit"]');
        const requiredInputs = form.querySelectorAll('input[required], select[required]');

        function updateButtonState() {
            let allFilled = true;
            requiredInputs.forEach(function (input) {
                if (!input.value.trim()) {
                    allFilled = false;
                }
            });
            submitButton.disabled = !allFilled;
        }

        updateButtonState();
        requiredInputs.forEach(function (input) {
            input.addEventListener('input', updateButtonState);
            input.addEventListener('change', updateButtonState);
        });
    });
});
