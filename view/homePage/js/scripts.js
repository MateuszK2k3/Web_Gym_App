window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
});

function rozwinPlan(button) {
    // Znajdź najbliższy element rodzica z klasą `plan`, a potem jego sąsiedni element `.details`
    const plan = button.closest('.plan');
    const details = plan ? plan.nextElementSibling : null;

    if (!details || !details.classList.contains('details')) {
        console.error("Nie znaleziono elementu `.details`.");
        return;
    }

    // Sprawdź obecny stan elementu i zastosuj animację
    if (details.style.maxHeight === '0px' || details.style.maxHeight === '') {
        details.style.maxHeight = details.scrollHeight + "px"; // Rozwiń element
        details.style.padding = '10px 20px'; // Dodaj padding podczas rozwijania
    } else {
        details.style.maxHeight = '0px'; // Zwiń element
        details.style.padding = '0 20px'; // Usuń padding podczas zwijania
    }
}


function deleteTrainingPlan(planId) {
    if (confirm("Czy na pewno chcesz usunąć ten plan treningowy?")) {
        const formData = new FormData();
        formData.append('delete_plan_id', planId);

        fetch('', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                location.reload(); // Odśwież stronę
            })
            .catch(error => console.error('Błąd:', error));
    }
}

function deleteSessionPlan(sessionId) {
    if (!sessionId) {
        console.error('Brak sessionId do usunięcia.');
        return;
    }

    if (confirm("Czy na pewno chcesz usunąć ten plan treningowy?")) {
        const formData = new FormData();
        formData.append('session_id', sessionId);

        fetch('', { // Adres punktu końcowego
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                location.reload(); // Odśwież stronę po usunięciu
            })
            .catch(error => {
                console.error('Błąd podczas wysyłania żądania:', error);
            });
    }
}



function activateTrainingPlan(planId) {
    const formData = new FormData();
    formData.append('activate_plan_id', planId);

    fetch('', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(data => {
            location.reload(); // Odśwież stronę
        })
        .catch(error => console.error('Błąd:', error));
}



// FORMULARZ PLANU TRENINGOWEGO




let selectedDays = 0;

document.addEventListener("DOMContentLoaded", () => {
    const radioGroup = document.getElementById("radio-group");

    // Generowanie opcji dni
    for (let i = 1; i <= 7; i++) {
        const input = document.createElement("input");
        input.type = "radio";
        input.id = `day-${i}`;
        input.name = "days";
        input.value = i;

        const label = document.createElement("label");
        label.setAttribute("for", `day-${i}`);
        label.textContent = `${i} dni`;
        label.classList.add("radio-option");

        if(radioGroup){
            radioGroup.appendChild(input);
            radioGroup.appendChild(label);
        }
    }
});


function toggleContent(contentId) {
    const collapsible = document.getElementById(contentId);

    if (collapsible.style.maxHeight) {
        // Jeżeli jest ustawiona max-height, oznacza to, że zawartość jest rozwinięta
        collapsible.style.maxHeight = null;
    } else {
        // Ustawienie dynamicznego scrollHeight jako maxHeight
        collapsible.style.maxHeight = collapsible.scrollHeight + "px";
    }
}

function expandFirst(){
    let container = document.getElementById("form-container");
    container.style.visibility = "visible"

    let button = document.getElementById("start-form-button");
    button.style.visibility = "hidden"

    toggleContent("form-step-1");
}

function cancelForm(form) {
    if(form === 1){
        toggleContent("form-step-1");
    }
    else {
        toggleContent("form-step-2");
    }
    let container = document.getElementById("form-container");
    container.style.visibility = "hidden"

    let button = document.getElementById("start-form-button");
    button.style.visibility = "visible"
}

function cancelForm2() {
    window.location.href="mainPage.php?page=trainings";
}

function showNextStep() {
    const selectedDaysRadio = document.querySelector('input[name="days"]:checked');
    if (!selectedDaysRadio) {
        alert("Wybierz ilość dni treningowych!");
        return;
    }

    selectedDays = parseInt(selectedDaysRadio.value);

    // Generowanie dni treningowych
    const container = document.getElementById("training-days-container");
    container.innerHTML = ""; // Czyścimy poprzednią zawartość
    for (let i = 1; i <= selectedDays; i++) {
        const dayDiv = document.createElement("div");
        dayDiv.classList.add("training-day");
        dayDiv.innerHTML = `
            <h3>Dzień ${i}</h3>
            <div class="exercise-group">
                <input type="text" class="exercise-name" name="exercise_day_${i}_1" placeholder="Ćwiczenie">
                <input type="number" class="series-count" name="series_day_${i}_1" placeholder="Serie" min="1">
            </div>
            <button type="button" class="next-ex-button" onclick="addExercise(${i})"><i class="fas fa-plus"></i>Dodaj ćwiczenie</button>
            <button type="button" class="undo-button" style="display:none;" onclick="undoExercise(${i})">Cofnij</button>
        `;
        container.appendChild(dayDiv);
    }

    toggleContent("form-step-1");
    toggleContent("form-step-2");
}

function showNextSessionStep() {
    toggleContent("form-step-1");
    toggleContent("form-step-2");
}
function addExercise(day) {
    const dayDiv = document.querySelector(`.training-day:nth-child(${day})`);
    const exerciseCount = dayDiv.querySelectorAll(".exercise-group").length + 1;

    const exerciseGroup = document.createElement("div");
    exerciseGroup.classList.add("exercise-group");
    exerciseGroup.innerHTML = `
        <input type="text" class="exercise-name" name="exercise_day_${day}_${exerciseCount}" placeholder="Ćwiczenie">
        <input type="number" class="series-count" name="series_day_${day}_${exerciseCount}" placeholder="Serie" min="1">
    `;
    dayDiv.insertBefore(exerciseGroup, dayDiv.querySelector("button"));

    // Pokaż przycisk cofnij, jeżeli dodano co najmniej 2 ćwiczenia
    const undoButton = dayDiv.querySelector(".undo-button");
    const exerciseGroups = dayDiv.querySelectorAll(".exercise-group");
    if (exerciseGroups.length > 1) {
        undoButton.style.display = "inline-block";
    }

    // Zaktualizuj wysokość kontenera
    updateContainerHeight("form-step-2");
}
function undoExercise(day) {
    const dayDiv = document.querySelector(`.training-day:nth-child(${day})`);
    const exerciseGroups = dayDiv.querySelectorAll(".exercise-group");

    // Usuwamy ostatnią grupę ćwiczeń
    if (exerciseGroups.length > 0) {
        exerciseGroups[exerciseGroups.length - 1].remove();
    }

    // Ukryj przycisk cofnij, jeśli pozostało tylko jedno ćwiczenie
    const undoButton = dayDiv.querySelector(".undo-button");
    if (exerciseGroups.length <= 1) {
        undoButton.style.display = "none";
    }
}
function updateContainerHeight(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        container.style.maxHeight = `${container.scrollHeight}px`;
    }
}

function createHiddenInput(name, value = "") {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value || "Brak ćwiczenia";
    return input;
}
function submitTrainingPlan() {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "process.php";

    // Dodanie wszystkich danych z formularza do POST
    const nameInput = document.getElementById("name");
    const daysInput = document.querySelector('input[name="days"]:checked');

    if (nameInput) {
        form.appendChild(createHiddenInput("name", nameInput.value));
    }

    if (daysInput) {
        form.appendChild(createHiddenInput("days", daysInput.value));
    }
}

// SESJE PLANÓW TRENINGOWYCH


function toggleDateOptions() {
    const todayRadio = document.getElementById('today-radio');
    const customRadio = document.getElementById('custom-radio');
    const todayDate = document.getElementById('today-date');
    const customDateField = document.getElementById('custom-date-field');

    if (todayRadio.checked) {
        // Pokaż dzisiejszą datę, ukryj pole własnej daty
        todayDate.style.display = 'inline-block';
        customDateField.classList.remove('show');
    } else if (customRadio.checked) {
        // Pokaż pole własnej daty, ukryj dzisiejszą datę
        todayDate.style.display = 'none';
        customDateField.classList.add('show');
    }
}


