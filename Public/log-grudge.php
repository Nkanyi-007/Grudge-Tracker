<?php
$pageTitle = "Log a Grudge — Grudge Tracker";
include 'includes/header.php';
?>

<div class="dashboard-top">
<h1 class="graffiti-heading heading-cyan-pink">GET IT OFF YOUR CHEST</h1>
</div>

<div class="evidence-card log-grudge-card">
  <div class="tape tape-left"></div>
  <div class="tape tape-right"></div>

  <form action="#" method="POST" enctype="multipart/form-data" class="grudge-form">

    <div class="form-group">
      <label for="title">WHAT HAPPENED</label>
      <textarea id="title" name="title" rows="3" placeholder="Tell it exactly how it went down..." required></textarea>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="person">PERSON INVOLVED</label>
        <input type="text" id="person" name="person" placeholder="Who wronged you?" required>
      </div>

      <div class="form-group">
        <label for="category">CATEGORY</label>
        <select id="category" name="category" required>
          <option value="">Select category</option>
          <option value="Roommate">Roommate</option>
          <option value="Work">Work</option>
          <option value="Friend">Friend</option>
          <option value="Family">Family</option>
          <option value="Stranger">Stranger</option>
          <option value="Partner">Partner</option>
          <option value="Other">Other</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="date">DATE IT HAPPENED</label>
        <input type="date" id="date" name="date" required>
      </div>

      <div class="form-group">
        <label for="severity">SEVERITY</label>
        <select id="severity" name="severity" required>
          <option value="">Select severity</option>
          <option value="Low">Low — mildly annoying</option>
          <option value="Medium">Medium — actually irritating</option>
          <option value="High">High — cannot let this go</option>
          <option value="Critical">Critical — blood feud</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>HOW DOES IT FEEL</label>
      <div class="emoji-picker" id="emojiPicker">
        <button type="button" class="emoji-option" data-emoji="😤">😤</button>
        <button type="button" class="emoji-option" data-emoji="😡">😡</button>
        <button type="button" class="emoji-option" data-emoji="💀">💀</button>
        <button type="button" class="emoji-option" data-emoji="🙄">🙄</button>
        <button type="button" class="emoji-option" data-emoji="😭">😭</button>
        <button type="button" class="emoji-option" data-emoji="🔥">🔥</button>
      </div>
      <input type="hidden" id="selectedEmoji" name="emoji">
    </div>

    <div class="form-group">
      <label for="evidence">EVIDENCE (SCREENSHOTS, RECEIPTS, ETC.)</label>
      <div class="evidence-upload">
        <input type="file" id="evidence" name="evidence[]" multiple accept="image/*,.pdf">
        <p class="upload-hint">Drop files or click to upload — the jury will want proof.</p>
      </div>
    </div>

    <div class="form-group">
      <label for="notes">ADDITIONAL NOTES</label>
      <textarea id="notes" name="notes" rows="3" placeholder="Anything else the court should know..."></textarea>
    </div>

    <button type="submit" class="btn-sticker btn-submit-grudge">FILE THE GRUDGE</button>

  </form>
</div>

<?php include 'includes/footer.php'; ?>