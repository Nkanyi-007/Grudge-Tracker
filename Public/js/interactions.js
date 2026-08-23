function filterGrudges() {
  const searchValue = document.getElementById('searchInput').value.toLowerCase();
  const severityValue = document.getElementById('severityFilter').value;
  const statusValue = document.getElementById('statusFilter').value;
  const sortValue = document.getElementById('sortSelect').value;

  const grid = document.getElementById('grudgeGrid');
  if (!grid) return;

  const cards = Array.from(grid.querySelectorAll('.grudge-card'));
  let visibleCount = 0;

  cards.forEach(card => {
    const matchesSearch = card.dataset.title.includes(searchValue);
    const matchesSeverity = !severityValue || card.dataset.severity === severityValue;
    const matchesStatus = !statusValue || card.dataset.status === statusValue;
    const isVisible = matchesSearch && matchesSeverity && matchesStatus;
    card.style.display = isVisible ? '' : 'none';
    if (isVisible) visibleCount++;
  });

  document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';

  // Sort (severity order only, since date sort needs real timestamps later)
  if (sortValue === 'severity') {
    const order = { 'Critical': 0, 'High': 1, 'Medium': 2, 'Low': 3 };
    cards.sort((a, b) => order[a.dataset.severity] - order[b.dataset.severity]);
    cards.forEach(card => grid.appendChild(card));
  }
}

// Emoji picker for log-grudge page
document.addEventListener('DOMContentLoaded', function () {
  const emojiButtons = document.querySelectorAll('.emoji-option');
  const emojiInput = document.getElementById('selectedEmoji');

  emojiButtons.forEach(btn => {
    btn.addEventListener('click', function () {
      emojiButtons.forEach(b => b.classList.remove('selected'));
      this.classList.add('selected');
      if (emojiInput) emojiInput.value = this.dataset.emoji;
    });
  });
});

// Jury verdict modal
let selectedVerdict = null;

function openVerdictModal() {
  const overlay = document.getElementById('verdictModalOverlay');
  if (overlay) overlay.classList.add('active');
}

function closeVerdictModal() {
  const overlay = document.getElementById('verdictModalOverlay');
  if (overlay) overlay.classList.remove('active');
  selectedVerdict = null;
  document.querySelectorAll('.verdict-choice-btn').forEach(btn => btn.classList.remove('selected'));
  const confirmMsg = document.getElementById('verdictConfirmMsg');
  if (confirmMsg) confirmMsg.textContent = '';
}

function selectVerdict(choice) {
  selectedVerdict = choice;
  document.querySelectorAll('.verdict-choice-btn').forEach(btn => btn.classList.remove('selected'));
  const chosenBtn = document.querySelector('.choice-' + choice);
  if (chosenBtn) chosenBtn.classList.add('selected');
}

function submitVerdict() {
  const confirmMsg = document.getElementById('verdictConfirmMsg');
  if (!selectedVerdict) {
    confirmMsg.textContent = "Pick guilty or innocent before submitting.";
    confirmMsg.style.color = "#FF2D7A";
    return;
  }
  // This is where the real vote would POST to the backend later
  confirmMsg.textContent = "Verdict recorded: " + selectedVerdict.toUpperCase() + ". Thanks for weighing in.";
  confirmMsg.style.color = "#52FF6B";
  setTimeout(closeVerdictModal, 1800);
}

// Jury verdict modal
function openVerdictModal() {
  const overlay = document.getElementById('verdictModalOverlay');
  if (overlay) overlay.classList.add('active');
}

function closeVerdictModal() {
  const overlay = document.getElementById('verdictModalOverlay');
  if (overlay) overlay.classList.remove('active');
}

function selectVerdict(choice) {
  document.querySelectorAll('.verdict-choice-btn').forEach(btn => btn.classList.remove('selected'));
  document.getElementById(choice === 'guilty' ? 'choiceGuilty' : 'choiceInnocent').classList.add('selected');
}

function toggleForm(id) {
  const form = document.getElementById(id);
  if (form) {
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
  }
}