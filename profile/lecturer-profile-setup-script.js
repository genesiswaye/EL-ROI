// ==========================================
// LECTURER PROFILE SETUP - JAVASCRIPT
// StudentLancer Platform
// ==========================================

// State
const researchInterests = new Set();
const expertiseAreas = new Set();

// Profile completion tracking
const completionFields = {
  department: false,
  rank: false,
  research: false,
  expertise: false,
  bio: false,
};

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  // Restore database values

  existingResearch.forEach((item) => {
    researchInterests.add(item);

    const btn = document.querySelector(`[data-interest="${item}"]`);

    if (btn) {
      btn.classList.add("active");
    }
  });

  renderResearchInterests();

  existingExpertise.forEach((item) => {
    expertiseAreas.add(item);
  });

  renderExpertiseAreas();

  updateCharCount();
  updateCompletion();

  console.log("Lecturer profile setup initialized");
});

// ==========================================
// PROFILE COMPLETION CALCULATION
// ==========================================
function updateCompletion() {
  // Check department
  const department = document.getElementById("department").value;
  completionFields.department = department !== "";
  updateChecklistItem("check-department", completionFields.department);

  // Check academic rank
  const rank = document.getElementById("academicRank").value;
  completionFields.rank = rank !== "";
  updateChecklistItem("check-rank", completionFields.rank);

  // Check research interests
  completionFields.research = researchInterests.size > 0;
  updateChecklistItem("check-research", completionFields.research);

  // Check expertise areas
  completionFields.expertise = expertiseAreas.size > 0;
  updateChecklistItem("check-expertise", completionFields.expertise);

  // Check biography (minimum 100 characters)
  const bio = document.getElementById("biography").value;
  completionFields.bio = bio.length >= 100;
  updateChecklistItem("check-bio", completionFields.bio);

  // Calculate percentage
  const completed = Object.values(completionFields).filter(Boolean).length;
  const total = Object.keys(completionFields).length;
  const percentage = Math.round((completed / total) * 100);

  // Update UI
  updateStrengthMeter(percentage);
  updateBadge(percentage);
  updateSummary(percentage);
}

function updateChecklistItem(id, completed) {
  const item = document.getElementById(id);
  if (!item) return;

  if (completed) {
    item.classList.add("completed");
    item.querySelector(".check-icon").textContent = "✓";
  } else {
    item.classList.remove("completed");
    item.querySelector(".check-icon").textContent = "○";
  }
}

function updateStrengthMeter(percentage) {
  const fill = document.getElementById("strengthFill");
  const percentageElement = document.getElementById("strengthPercentage");

  fill.style.width = percentage + "%";
  percentageElement.textContent = percentage + "%";
}

function updateBadge(percentage) {
  const badge = document.getElementById("strengthBadge");

  let level = "Beginner";

  if (percentage === 100) {
    level = "Expert";
  } else if (percentage >= 80) {
    level = "Advanced";
  } else if (percentage >= 60) {
    level = "Intermediate";
  } else if (percentage >= 40) {
    level = "Developing";
  }

  badge.textContent = level;
}

function updateSummary(percentage) {
  const summaryPercentage = document.getElementById("summaryPercentage");
  const summaryFill = document.getElementById("summaryFill");
  const summaryMessage = document.getElementById("summaryMessage");

  summaryPercentage.textContent = percentage + "%";
  summaryFill.style.width = percentage + "%";

  let message = "Complete all sections to unlock your verified badge";

  if (percentage === 100) {
    message = "Excellent! Your profile is complete and ready for verification";
  } else if (percentage >= 80) {
    message = "Almost there! Just a few more details needed";
  } else if (percentage >= 60) {
    message = "Good progress! Keep going to strengthen your profile";
  } else if (percentage >= 40) {
    message = "You're making progress! Add more details to stand out";
  }

  summaryMessage.textContent = message;
}

// ==========================================
// RESEARCH INTERESTS MANAGEMENT
// ==========================================
function toggleInterest(button) {
  const interest = button.getAttribute("data-interest");

  if (button.classList.contains("active")) {
    // Deselect
    button.classList.remove("active");
    researchInterests.delete(interest);
  } else {
    // Select (max 10)
    if (researchInterests.size >= 10) {
      showNotification("Maximum 10 research interests allowed", "warning");
      return;
    }
    button.classList.add("active");
    researchInterests.add(interest);
  }

  renderResearchInterests();
  updateCompletion();
  autoSaveDraft();
}

function handleInterestInput(event) {
  if (event.key === "Enter") {
    event.preventDefault();

    const input = event.target;
    const value = input.value.trim();

    if (value.length === 0) return;

    if (researchInterests.has(value)) {
      showNotification("This research interest is already added", "warning");
      input.value = "";
      return;
    }

    if (researchInterests.size >= 10) {
      showNotification("Maximum 10 research interests allowed", "warning");
      input.value = "";
      return;
    }

    researchInterests.add(value);
    input.value = "";

    renderResearchInterests();
    updateCompletion();
    autoSaveDraft();
  }
}

function renderResearchInterests() {
  const container = document.getElementById("selectedInterests");

  if (researchInterests.size === 0) {
    container.innerHTML = "";
    return;
  }

  container.innerHTML = Array.from(researchInterests)
    .map(
      (interest) => `
    <div class="interest-tag">
      <span>${interest}</span>
      <button type="button" class="remove-tag" onclick="removeInterest('${interest.replace(/'/g, "\\'")}')">×</button>
    </div>
  `,
    )
    .join("");
}

function removeInterest(interest) {
  researchInterests.delete(interest);

  // Deactivate button if it's a predefined interest
  const buttons = document.querySelectorAll(".interest-chip");
  buttons.forEach((button) => {
    if (button.getAttribute("data-interest") === interest) {
      button.classList.remove("active");
    }
  });

  renderResearchInterests();
  updateCompletion();
  autoSaveDraft();
}

// ==========================================
// EXPERTISE AREAS MANAGEMENT
// ==========================================
function handleExpertiseInput(event) {
  if (event.key === "Enter") {
    event.preventDefault();

    const input = event.target;
    const value = input.value.trim();

    if (value.length === 0) return;

    if (expertiseAreas.has(value)) {
      showNotification("This expertise area is already added", "warning");
      input.value = "";
      return;
    }

    if (expertiseAreas.size >= 15) {
      showNotification("Maximum 15 areas of expertise allowed", "warning");
      input.value = "";
      return;
    }

    expertiseAreas.add(value);
    input.value = "";

    renderExpertiseAreas();
    updateCompletion();
    autoSaveDraft();
  }
}

function renderExpertiseAreas() {
  const container = document.getElementById("expertiseTags");

  if (expertiseAreas.size === 0) {
    container.innerHTML = "";
    return;
  }

  container.innerHTML = Array.from(expertiseAreas)
    .map(
      (expertise) => `
    <div class="expertise-tag">
      <span>${expertise}</span>
      <button type="button" class="remove-tag" onclick="removeExpertise('${expertise.replace(/'/g, "\\'")}')">×</button>
    </div>
  `,
    )
    .join("");
}

function removeExpertise(expertise) {
  expertiseAreas.delete(expertise);
  renderExpertiseAreas();
  updateCompletion();
  autoSaveDraft();
}

// ==========================================
// CHARACTER COUNT
// ==========================================
function updateCharCount() {
  const bio = document.getElementById("biography");
  const charCount = document.getElementById("charCount");
  const current = bio.value.length;
  const max = 1000;

  charCount.textContent = `${current} / ${max}`;

  if (current > max * 0.9) {
    charCount.style.color = "#DC2626";
  } else if (current > max * 0.7) {
    charCount.style.color = "#D97706";
  } else {
    charCount.style.color = "#94A3B8";
  }
}

// ==========================================
// FORM SUBMISSION
// ==========================================
document
  .getElementById("lecturerForm")
  .addEventListener("submit", function (e) {
    if (researchInterests.size === 0) {
      e.preventDefault();
      showNotification("Please add at least one research interest", "error");
      return;
    }

    if (expertiseAreas.size === 0) {
      e.preventDefault();
      showNotification("Please add at least one area of expertise", "error");
      return;
    }

    if (document.getElementById("biography").value.length < 100) {
      e.preventDefault();

      showNotification("Biography must be at least 100 characters", "error");

      return;
    }

    document.getElementById("researchHidden").value = JSON.stringify(
      Array.from(researchInterests),
    );

    document.getElementById("expertiseHidden").value = JSON.stringify(
      Array.from(expertiseAreas),
    );
  });

// ==========================================
// PREVIEW PROFILE
// ==========================================
function previewProfile() {
  const modal = document.getElementById("previewModal");
  const previewBody = document.getElementById("previewBody");

  const department =
    document.getElementById("department").value || "Not specified";
  const rank = document.getElementById("academicRank").value || "Not specified";
  const bio =
    document.getElementById("biography").value || "No biography added yet";

  previewBody.innerHTML = `
    <div style="margin-bottom: 24px;">
      <h4 style="font-size: 14px; color: #64748B; margin-bottom: 8px;">Department</h4>
      <p style="font-size: 16px; color: #1E293B; font-weight: 600;">${department}</p>
    </div>
    
    <div style="margin-bottom: 24px;">
      <h4 style="font-size: 14px; color: #64748B; margin-bottom: 8px;">Academic Rank</h4>
      <p style="font-size: 16px; color: #1E293B; font-weight: 600;">${rank}</p>
    </div>
    
    <div style="margin-bottom: 24px;">
      <h4 style="font-size: 14px; color: #64748B; margin-bottom: 8px;">Research Interests</h4>
      <div style="display: flex; flex-wrap: wrap; gap: 8px;">
        ${
          researchInterests.size > 0
            ? Array.from(researchInterests)
                .map(
                  (interest) =>
                    `<span style="padding: 6px 12px; background: linear-gradient(135deg, #1E40AF 0%, #7C3AED 100%); color: white; border-radius: 100px; font-size: 13px;">${interest}</span>`,
                )
                .join("")
            : '<p style="color: #94A3B8;">No research interests added</p>'
        }
      </div>
    </div>
    
    <div style="margin-bottom: 24px;">
      <h4 style="font-size: 14px; color: #64748B; margin-bottom: 8px;">Areas of Expertise</h4>
      <div style="display: flex; flex-wrap: wrap; gap: 8px;">
        ${
          expertiseAreas.size > 0
            ? Array.from(expertiseAreas)
                .map(
                  (expertise) =>
                    `<span style="padding: 6px 12px; background: linear-gradient(135deg, #0D9488 0%, #14B8A6 100%); color: white; border-radius: 100px; font-size: 13px;">${expertise}</span>`,
                )
                .join("")
            : '<p style="color: #94A3B8;">No expertise areas added</p>'
        }
      </div>
    </div>
    
    <div>
      <h4 style="font-size: 14px; color: #64748B; margin-bottom: 8px;">Academic Biography</h4>
      <p style="font-size: 15px; color: #1E293B; line-height: 1.7; white-space: pre-wrap;">${bio}</p>
    </div>
  `;

  modal.classList.add("active");
}

function closePreview() {
  const modal = document.getElementById("previewModal");
  modal.classList.remove("active");
}

// ==========================================
// NOTIFICATION SYSTEM
// ==========================================
function showNotification(message, type = "info") {
  const existing = document.querySelector(".notification");
  if (existing) existing.remove();

  const notification = document.createElement("div");
  notification.className = `notification notification-${type}`;

  const colors = {
    success: "#059669",
    error: "#DC2626",
    warning: "#D97706",
    info: "#1E40AF",
  };

  const icons = {
    success: "✓",
    error: "✗",
    warning: "⚠",
    info: "ℹ",
  };

  notification.style.cssText = `
    position: fixed;
    top: 100px;
    right: 24px;
    background: ${colors[type]};
    color: white;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    z-index: 10000;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    animation: slideInRight 0.3s ease-out;
    max-width: 400px;
  `;

  notification.innerHTML = `
    <span style="font-size: 20px;">${icons[type]}</span>
    <span>${message}</span>
  `;

  document.body.appendChild(notification);

  setTimeout(() => {
    notification.style.animation = "slideOutRight 0.3s ease-out";
    setTimeout(() => notification.remove(), 300);
  }, 4000);
}

// Add animation styles
const style = document.createElement("style");
style.textContent = `
  @keyframes slideInRight {
    from { opacity: 0; transform: translateX(100px); }
    to { opacity: 1; transform: translateX(0); }
  }
  @keyframes slideOutRight {
    from { opacity: 1; transform: translateX(0); }
    to { opacity: 0; transform: translateX(100px); }
  }
`;
document.head.appendChild(style);

// ==========================================
// AUTO-SAVE DRAFT
// ==========================================
let autoSaveTimeout;

function autoSaveDraft() {
  clearTimeout(autoSaveTimeout);

  autoSaveTimeout = setTimeout(() => {
    const draft = {
      department: document.getElementById("department").value,
      academicRank: document.getElementById("academicRank").value,
      researchInterests: Array.from(researchInterests),
      expertiseAreas: Array.from(expertiseAreas),
      biography: document.getElementById("biography").value,
    };

    localStorage.setItem("lecturerProfileDraft", JSON.stringify(draft));
    console.log("Draft auto-saved");
  }, 2000);
}

function loadDraft() {
  const draft = localStorage.getItem("lecturerProfileDraft");

  if (!draft) return;

  try {
    const data = JSON.parse(draft);

    if (data.department)
      document.getElementById("department").value = data.department;
    if (data.academicRank)
      document.getElementById("academicRank").value = data.academicRank;
    if (data.biography)
      document.getElementById("biography").value = data.biography;

    // Restore research interests
    if (data.researchInterests) {
      data.researchInterests.forEach((interest) => {
        researchInterests.add(interest);

        // Activate predefined button if exists
        const button = document.querySelector(`[data-interest="${interest}"]`);
        if (button) button.classList.add("active");
      });
      renderResearchInterests();
    }

    // Restore expertise areas
    if (data.expertiseAreas) {
      data.expertiseAreas.forEach((expertise) => {
        expertiseAreas.add(expertise);
      });
      renderExpertiseAreas();
    }

    updateCompletion();
    updateCharCount();

    console.log("Draft loaded from localStorage");
  } catch (e) {
    console.error("Error loading draft:", e);
  }
}

// Attach auto-save to inputs
document.querySelectorAll("select, textarea").forEach((element) => {
  element.addEventListener("change", autoSaveDraft);
  element.addEventListener("input", autoSaveDraft);
});

const departmentSelect = document.getElementById("department");

const otherContainer = document.getElementById("otherDepartmentContainer");

function toggleOtherDepartment() {
  if (departmentSelect.value === "Other") {
    otherContainer.style.display = "block";
  } else {
    otherContainer.style.display = "none";
  }
}

departmentSelect.addEventListener("change", toggleOtherDepartment);

toggleOtherDepartment();

// ==========================================
// KEYBOARD SHORTCUTS
// ==========================================
document.addEventListener("keydown", (e) => {
  // Ctrl/Cmd + S = Save profile
  if ((e.ctrlKey || e.metaKey) && e.key === "s") {
    e.preventDefault();
    document.getElementById("lecturerForm").dispatchEvent(new Event("submit"));
  }

  // Ctrl/Cmd + P = Preview
  if ((e.ctrlKey || e.metaKey) && e.key === "p") {
    e.preventDefault();
    previewProfile();
  }

  // ESC = Close preview
  if (e.key === "Escape") {
    closePreview();
  }
});

// ==========================================
// FORM ENHANCEMENTS
// ==========================================
// Close preview when clicking outside
document.getElementById("previewModal")?.addEventListener("click", (e) => {
  if (e.target.classList.contains("preview-modal")) {
    closePreview();
  }
});
