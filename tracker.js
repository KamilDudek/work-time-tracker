document.addEventListener('DOMContentLoaded', () => {
  const projectSearchInput = document.getElementById('project-search');
  const projectDatalist = document.getElementById('project-list');
  const startBtn = document.getElementById('start-btn');
  const stopBtn = document.getElementById('stop-btn');
  const timerDisplay = document.getElementById('timer-display');
  const stopModal = document.getElementById('stop-modal');
  const confirmStopBtn = document.getElementById('confirm-stop-btn');
  const cancelStopBtn = document.getElementById('cancel-stop-btn');
  const workDescription = document.getElementById('work-description');
  const userInfoSpan = document.querySelector('.user-info strong');

  let timerInterval;
  let seconds = 0;
  let workEntryId = null;
  let projects = [];
  let selectedProjectId = null;

  // --- API Communication ---
  async function apiCall(action, data = {}) {
    const options = {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    };
    try {
      const response = await fetch(`api.php?action=${action}`, options);
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
      }
      return await response.json();
    } catch (error) {
      console.error('API Error:', error);
      alert(`Wystąpił błąd: ${error.message}`);
      return null;
    }
  }

  // --- Initialization ---
  async function initializeTracker() {
    const data = await apiCall('get_initial_data');
    if (!data || data.status !== 'success') {
      alert('Nie udało się załadować danych. Proszę spróbować zalogować się ponownie.');
      window.location.href = 'index.php';
      return;
    }

    // Populate user name
    userInfoSpan.textContent = data.employeeName;

    // Populate projects
    projects = data.projects;
    projectDatalist.innerHTML = '';
    projects.forEach(p => {
      const option = document.createElement('option');
      option.value = `${p.nazwa} - ${p.opis}`;
      option.dataset.id = p.id;
      projectDatalist.appendChild(option);
    });

    // Check for ongoing work
    if (data.ongoingWork) {
      const { workEntryId: ongoingId, projectId, startTime } = data.ongoingWork;
      workEntryId = ongoingId;
      const currentProject = projects.find(p => p.id == projectId);
      if (currentProject) {
        projectSearchInput.value = `${currentProject.nazwa} - ${currentProject.opis}`;
        selectedProjectId = currentProject.id;
      }

      const serverTime = Math.floor(Date.now() / 1000);
      seconds = serverTime - startTime;

      setControlsToRunningState();
      timerInterval = setInterval(updateTimer, 1000);
    }
  }

  // --- UI Event Listeners ---
  projectSearchInput.addEventListener('input', () => {
    const inputValue = projectSearchInput.value;
    const option = Array.from(projectDatalist.options).find(opt => opt.value === inputValue);
    if (option) {
      selectedProjectId = option.dataset.id;
    } else {
      selectedProjectId = null;
    }
  });

  startBtn.addEventListener('click', async () => {
    if (!selectedProjectId) {
      alert('Proszę wybrać prawidłowy projekt z listy.');
      return;
    }

    const result = await apiCall('start_work', { projectId: selectedProjectId });
    if (result && result.status === 'success') {
      workEntryId = result.workEntryId;
      seconds = 0;
      setControlsToRunningState();
      timerInterval = setInterval(updateTimer, 1000);
    }
  });

  stopBtn.addEventListener('click', () => {
    stopModal.style.display = 'flex';
  });

  cancelStopBtn.addEventListener('click', () => {
    stopModal.style.display = 'none';
  });

  confirmStopBtn.addEventListener('click', async () => {
    clearInterval(timerInterval);
    const description = workDescription.value;

    const result = await apiCall('stop_work', { workEntryId, description });
    if (result && result.status === 'success') {
      stopModal.style.display = 'none';
      setControlsToStoppedState();
    } else {
      // If API call fails, restart the timer
      timerInterval = setInterval(updateTimer, 1000);
    }
  });

  // --- Helper Functions ---
  function setControlsToRunningState() {
    startBtn.disabled = true;
    stopBtn.disabled = false;
    projectSearchInput.disabled = true;
  }

  function setControlsToStoppedState() {
    startBtn.disabled = false;
    stopBtn.disabled = true;
    projectSearchInput.disabled = false;
    projectSearchInput.value = '';
    timerDisplay.textContent = '00:00:00';
    workDescription.value = '';
    workEntryId = null;
    selectedProjectId = null;
  }

  function updateTimer() {
    seconds++;
    const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    timerDisplay.textContent = `${h}:${m}:${s}`;
  }

  // --- Initial Load ---
  initializeTracker();
});
