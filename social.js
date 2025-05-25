document.getElementById("submit").addEventListener("click", submit);

function submit(e) {
    e.preventDefault();

    const f_name = document.getElementById("f_name").value.trim();
    const l_name = document.getElementById("l_name").value.trim();
    const username = document.getElementById("u_name").value.trim();
    const bio = document.getElementById("bio").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const dob = document.getElementById("dob").value;
    const fileInput = document.getElementById("profile_img");
    const file = fileInput.files[0];

    const errorField = document.getElementById("error_field");

    if (!f_name || !l_name || !username || !email || !password || !dob) {
        errorField.textContent = "Please fill in all required fields.";
        return;
    } else {
        errorField.textContent = "";
    }

    const formData = new FormData();
    formData.append("f_name", f_name);
    formData.append("l_name", l_name);
    formData.append("username", username);
    formData.append("bio", bio);
    formData.append("email", email);
    formData.append("password", password);
    formData.append("dob", dob);
    if (file) {
        formData.append("file", file);
    }

    $.ajax({
        url: "http://localhost/student_management/db.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            alert("Server Response: " + response);
        },
        error: function (xhr, status, error) {
            alert("Error: " + error);
            console.error(xhr.responseText);
        }
    });
}