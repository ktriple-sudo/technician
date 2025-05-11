function validateClientForm() {
    let firstName = document.forms["clientForm"]["firstName"].value.trim();
    let lastName = document.forms["clientForm"]["lastName"].value.trim();
    let phone = document.forms["clientForm"]["Phone"].value.trim();

    if (!firstName || !lastName || !phone) {
        alert("All fields must be filled out for Client.");
        return false;
    }
    return true;
}

function validateSkillForm() {
    let skill = document.forms["skillForm"]["SkillName"].value.trim();
    if (!skill) {
        alert("Skill name is required.");
        return false;
    }
    return true;
}

function validateTechnicianForm() {
    let form = document.forms["technicianForm"];
    if (!form["firstName"].value.trim() ||
        !form["lastName"].value.trim() ||
        !form["Phone"].value.trim() ||
        !form["City"].value.trim()) {
        alert("Please fill in all required technician fields.");
        return false;
    }
    return true;
}

function validateAppointmentForm() {
    let form = document.forms["appointmentForm"];
    if (!form["ClientID"].value || !form["TechID"].value || !form["Date"].value) {
        alert("Please complete all fields to book an appointment.");
        return false;
    }
    return true;
}
