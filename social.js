document.getElementById("sumbit").addEventListener("click", submit);

function submit() {
    const f_name = document.getElementById("f_name").value;
    const l_name = document.getElementById("l_name").value;
    const username = document.getElementById("u_name").value;
    const bio = document.getElementById("bio").value;
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const dob = document.getElementById("dob").value;
    const fileInput = document.getElementById("profile_img");
    const file = fileInput.files[0];
    
    const userdata = {
        firstName: f_name,
        lastName: l_name,
        userName: username,
        bio: bio,
        email: email,
        password: password,
        dob: dob,
        file: file,
    };
    
    check(userdata);
}

function check(userdata) {
    const missingFields = [];
    const fieldLabels = {
        firstName: "First Name",
        lastName: "Last Name",
        userName: "Username",
        bio: "Bio",
        email: "Email",
        password: "Password",
        dob: "Date of Birth",
        file: "Profile Picture"
    };

    for (const key in userdata) {
        if (
            userdata[key] === "" ||
            userdata[key] === null ||
            userdata[key] === undefined
        ) {
            missingFields.push(fieldLabels[key] || key);
        }
    }
    if (missingFields.length > 0) {
        document.getElementById("error_field").innerHTML = "Please fill in the following fields: " + missingFields.join(", ");
    } else {
        document.getElementById("error_field").innerHTML = "";  // Clear error message
        console.log("done");
    }
}


