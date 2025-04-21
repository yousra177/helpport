// Toggle theme function
document.querySelector('.theme-toggle').addEventListener('click', function() {
    // Toggle the dark-theme class on the body element
    document.body.classList.toggle('dark-theme');
  });

          / admin option for creating users /

  document.querySelectorAll('.custom-select').forEach(
    select => { select.addEventListener('focus', function()
       { this.classList.add('active'); });
        select.addEventListener('blur', function()
         { if (this.value === '') { this.classList.remove('active');

          } }); });



          function confirmCancel() {
            const confirmation = confirm("Are you sure you want to cancel? All changes will be lost.");
            if (confirmation) {
              window.location.href = 'home.html'; // Redirect to home page or desired page
            }
          }

          document.getElementById('search-user').addEventListener('keyup', function () {
            let searchValue = this.value.toLowerCase();
            let rows = document.querySelectorAll("tbody tr");

            rows.forEach(row => {
                let name = row.children[0].textContent.toLowerCase();
                let email = row.children[1].textContent.toLowerCase();
                let phone = row.children[2].textContent.toLowerCase();
                let role = row.children[3].textContent.toLowerCase();
                let department = row.children[4].textContent.toLowerCase();

                if (name.includes(searchValue) || email.includes(searchValue) || phone.includes(searchValue) || role.includes(searchValue) || department.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });



  // Function to delete a user
  function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
      fetch(`/admin/users/${userId}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    }
  })
  .then(response => {
    if (!response.ok) {
      return response.text();  // Return the raw HTML response
    }
    return response.json();  // If successful, parse JSON
  })
  .then(data => {
    if (typeof data === 'string') {
      // Handle error (e.g., HTML error page)
      console.error('Error response:', data);
      alert('An error occurred: ' + data);
      return;
    }

    // Continue with success response handling
    if (data.success) {
      document.getElementById(`user-${userId}`).remove();
      alert('User deleted successfully');
    } else {
      alert('Error deleting user');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error occurred: ' + error.message);
  });
    }
  }
 // Initialize tooltips
 var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
 var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
     return new bootstrap.Tooltip(tooltipTriggerEl)
 })

 // Add smooth scrolling to solution links
 document.querySelectorAll('a[href^="#solution-"]').forEach(anchor => {
     anchor.addEventListener('click', function (e) {
         e.preventDefault();
         document.querySelector(this.getAttribute('href')).scrollIntoView({
             behavior: 'smooth'
         });
     });
 });

 // Handle attachment removal in edit modal
 document.addEventListener('DOMContentLoaded', function() {
     // When clicking remove attachment button
     document.querySelectorAll('.attachment-remove-btn').forEach(button => {
         button.addEventListener('click', function() {
             const attachment = this.getAttribute('data-attachment');
             const badge = this.closest('.attachment-badge');

             // Create a hidden input to mark this attachment for deletion
             const input = document.createElement('input');
             input.type = 'hidden';
             input.name = 'removed_attachments[]';
             input.value = attachment;

             // Add it to the form
             this.closest('form').appendChild(input);

             // Remove the visual element
             badge.remove();
         });
     });
 });



    // Function to delete a user
    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
          fetch(`/head/users/${userId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Content-Type': 'application/json'
            }
          })
          .then(response => response.json())
  .then(data => {
    console.log('Response Data:', data);  // Add this line
    if (data.success) {
      document.getElementById(`user-${userId}`).remove();
      alert('User deleted successfully');
    } else {
      alert('Error deleting user');
    }
  })
  .catch(error => {
    console.error('Error:', error);
  });

        }
      }
