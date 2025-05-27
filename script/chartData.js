const monthlyData = [100, 180, 300, 110, 350, 280, 190, 200, 260, 290, 240, 120];
        const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      
        const barChart = document.getElementById('barChart');
        const viewSelector = document.getElementById('viewSelector');
        const dataTable = document.getElementById('dataTable').querySelector('tbody');
        const dayPageSelector = document.getElementById('dayPageSelector');
        const tableContainer = document.querySelector('.table-container');
      
        let currentView = 'month';
        let selectedMonthIndex = new Date().getMonth();
        let currentPage = 0;
      
        const allDailyData = Array.from({ length: 12 }, (_, month) => {
          const days = new Date(new Date().getFullYear(), month + 1, 0).getDate();
          return Array.from({ length: days }, () => Math.floor(Math.random() * 400 + 50));
        });
      
        function renderMonthDropdown() {
          dayPageSelector.innerHTML = '';
          const currentMonth = new Date().getMonth();
          for (let i = 0; i <= currentMonth; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = monthsShort[i];
            if (i === currentMonth) option.selected = true;
            dayPageSelector.appendChild(option);
          }
          dayPageSelector.style.display = 'inline-block';
        }
      
        function renderPaginationButtons(totalItems) {
          let paginationDiv = document.getElementById('paginationButtons');
          if (!paginationDiv) {
            paginationDiv = document.createElement('div');
            paginationDiv.id = 'paginationButtons';
            paginationDiv.style.marginTop = '10px';
            paginationDiv.style.textAlign = 'center';
            tableContainer.appendChild(paginationDiv);
          }
      
          paginationDiv.innerHTML = '';
      
          const totalPages = Math.ceil(totalItems / 10);
      
          const prevBtn = document.createElement('button');
          prevBtn.innerHTML = '&#8592;';
          prevBtn.disabled = currentPage === 0;
          prevBtn.onclick = () => {
            if (currentPage > 0) {
              currentPage--;
              renderTable(selectedMonthIndex, currentPage);
            }
          };
      
          const nextBtn = document.createElement('button');
          nextBtn.innerHTML = '&#8594;';
          nextBtn.disabled = currentPage >= totalPages - 1;
          nextBtn.onclick = () => {
            if (currentPage < totalPages - 1) {
              currentPage++;
              renderTable(selectedMonthIndex, currentPage);
            }
          };
      
          paginationDiv.appendChild(prevBtn);
          paginationDiv.appendChild(document.createTextNode(` Page ${currentPage + 1} of ${totalPages} `));
          paginationDiv.appendChild(nextBtn);
        }
      
        function renderBars(view = 'month') {
            if (!barChart) return;
            barChart.innerHTML = '';
            const today = new Date();
            const currentMonth = today.getMonth();
          
            if (view === 'month') {
              for (let i = 0; i < 12; i++) {
                const barGroup = document.createElement('div');
                barGroup.className = 'bar-group';
          
                if (i <= currentMonth) {
                  const value = monthlyData[i];
                  const bar = document.createElement('div');
                  bar.className = 'bar';
                  bar.style.height = `${(value / 400) * 100 + 50}px`;
                  barGroup.appendChild(bar);
                }
          
                const label = document.createElement('div');
                label.className = 'label';
                label.textContent = monthsShort[i];
                barGroup.appendChild(label);
          
                barChart.appendChild(barGroup);
              }
            } else {
              const data = allDailyData[selectedMonthIndex];
              const isCurrentMonth = selectedMonthIndex === today.getMonth();
              const currentDay = today.getDate();
          
              data.forEach((value, i) => {
                const day = i + 1;
          
                if ((isCurrentMonth && day < currentDay) || (!isCurrentMonth)) {
                  const barGroup = document.createElement('div');
                  barGroup.className = 'bar-group';
          
                  const bar = document.createElement('div');
                  bar.className = 'bar';
                  bar.style.height = `${(value / 400) * 100 + 50}px`;
          
                  // ✅ Tooltip on hover
                  bar.title = `${value} applicant${value !== 1 ? 's' : ''} on Day ${day}`;
          
                  const label = document.createElement('div');
                  label.className = 'label';
                  label.textContent = day.toString();
          
                  barGroup.appendChild(bar);
                  barGroup.appendChild(label);
                  barChart.appendChild(barGroup);
                }
              });
            }
          }
      
        function renderTable(monthIndex, page = 0) {
          const monthData = allDailyData[monthIndex];
          dataTable.innerHTML = '';
      
          const today = new Date();
          const isCurrentMonth = monthIndex === today.getMonth();
          const daysInMonth = monthData.length;
          const startDayIndex = isCurrentMonth ? today.getDate() - 1 : daysInMonth - 1;
      
          const dayIndices = [];
          for (let i = startDayIndex; i >= 0; i--) {
            dayIndices.push(i);
          }
      
          const start = page * 9;
          const end = Math.min(start + 9, dayIndices.length);
      
          for (let i = start; i < end; i++) {
            const dayIndex = dayIndices[i];
            const row = document.createElement('tr');
            const dateCell = document.createElement('td');
            const monthCell = document.createElement('td');
            const valueCell = document.createElement('td');
      
            dateCell.textContent = (dayIndex + 1).toString();
            monthCell.textContent = monthsShort[monthIndex];
            valueCell.textContent = monthData[dayIndex];
      
            row.appendChild(dateCell);
            row.appendChild(monthCell);
            row.appendChild(valueCell);
            dataTable.appendChild(row);
          }
      
          renderPaginationButtons(dayIndices.length);
        }
      
        viewSelector?.addEventListener('change', (e) => {
          const selectedView = e.target.value;
          currentView = selectedView;
          if (selectedView === 'month') {
            document.getElementById('paginationButtons')?.remove();
            renderBars('month');
            renderTable(new Date().getMonth(), 0);
          } else {
            renderBars('day');
            renderTable(selectedMonthIndex, 0);
          }
        });
      
        dayPageSelector.addEventListener('change', (e) => {
          selectedMonthIndex = parseInt(e.target.value);
          currentPage = 0;
          renderBars(currentView);
          renderTable(selectedMonthIndex, currentPage);
        });
      
        // Initial load
        renderMonthDropdown();
        renderBars('month');
        renderTable(new Date().getMonth(), 0);
