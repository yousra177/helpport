function openViewModal(name, email, phoneNumber, role, department) {
    // Populate modal with user details
    document.getElementById("viewName").innerText = name;
    document.getElementById("viewEmail").innerText = email;
    document.getElementById("viewPhoneNumber").innerText = phoneNumber;
    document.getElementById("viewRole").innerText = role;
    document.getElementById("viewDepartment").innerText = department;

    // Display the modal
    document.getElementById("viewModal").style.display = "flex";
  }

  function closeViewModal() {
    // Hide the modal
    document.getElementById("viewModal").style.display = "none";
  }

  function openEditModal(name, email, phoneNumber, role, department) {
    // Populate the edit form with user data
    document.getElementById("editName").value = name;
    document.getElementById("editEmail").value = email;
    document.getElementById("editPhoneNumber").value = phoneNumber;
    document.getElementById("editRole").value = role;
    document.getElementById("editDepartment").value = department;

    // Display the modal
    document.getElementById("editModal").style.display = "flex";
  }

  function closeEditModal() {
    // Hide the modal
    document.getElementById("editModal").style.display = "none";
  }
