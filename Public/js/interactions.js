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