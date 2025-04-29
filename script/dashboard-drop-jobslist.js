
  const jobList = [
    { rank: "Tambay", vessel: "Navy", date: "12/12/2023", status: "Completed" },
    { rank: "Tambay", vessel: "Navy", date: "12/12/2023", status: "Completed" },
    { rank: "Tambay", vessel: "Navy", date: "12/12/2023", status: "In - progress" },
    { rank: "Tambay", vessel: "Navy", date: "12/12/2023", status: "Completed" },
    { rank: "Tambay", vessel: "Navy", date: "12/12/2023", status: "Rejected" },
  ];

  const tableBody = document.getElementById("tableBody");
  const dropdownBtn = document.getElementById("dropdownSelect");
  const dropdownList = document.getElementById("dropdownList");

  // Save the original HTML so we can restore it later
  const originalTableHTML = tableBody.innerHTML;

  dropdownBtn.addEventListener("click", () => {
    dropdownList.style.display = dropdownList.style.display === "block" ? "none" : "block";
  });

  dropdownList.querySelectorAll("li").forEach((item) => {
    item.addEventListener("click", () => {
      const type = item.getAttribute("data-type");

      if (type === "all") {
        renderjobList(jobList);
        dropdownBtn.innerHTML = `Job Post List <i class="fa-solid fa-angle-down"></i>`;
      } else if (type === "recent") {
        tableBody.innerHTML = originalTableHTML; // restore default HTML
        dropdownBtn.innerHTML = `Recent Job Post <i class="fa-solid fa-angle-down"></i>`;
      }

      dropdownList.style.display = "none";
    });
  });

  function renderjobList(data) {
    tableBody.innerHTML = data.map(item => `
      <tr class = "job-posted">
        <td data-label="Rank">${item.rank}</td>
        <td data-label="Vessel Type">${item.vessel}</td>
        <td data-label="Date">${item.date}</td>
        <td data-label="Status">
          <span class="badge ${getStatusClass(item.status)}">${item.status}</span>
        </td>
        <td>
          <button class="profile-side-btn" type="button" data-bs-toggle="modal" data-bs-target="#">
              <i class="fa-solid fa-pen-to-square"></i>
          </button>
        </td>
      </tr>
    `).join('');
  }

  function getStatusClass(status) {
    switch (status.toLowerCase()) {
      case 'completed': return 'completed';
      case 'rejected': return 'rejected';
      default: return 'in-progress';
    }
  }

