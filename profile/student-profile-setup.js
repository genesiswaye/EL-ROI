// ==========================================
// STUDENT PROFILE SETUP - JAVASCRIPT
// StudentLancer Platform
// ==========================================

// State
const selectedSkills = new Set();
const customTags = new Set();

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', () => {

    document
        .querySelectorAll('.skill-chip.active')
        .forEach(skill => {

            selectedSkills.add(
                skill.dataset.skill
            );

        });

    renderSelectedSkills();

    updateSkillsInput();

    updateCompletion();

    updateCharCount();

});

// ==========================================
// MATRIC NUMBER VALIDATION
// ==========================================
function validateMatricNumber(input) {
  const value = input.value.toUpperCase();
  input.value = value;
  
  // Matric number pattern: 00xx000000 (0 - numbers, x letters)
  const pattern = /^[A-Z0-9]+$/;
  const validationIcon = document.getElementById('matricValidation');
  
  if (value.length === 0) {
    input.classList.remove('valid');
    validationIcon.classList.remove('show');
    validationIcon.textContent = '';
    return;
  }
  
  if (pattern.test(value)) {
    input.classList.add('valid');
    validationIcon.textContent = '✓';
    validationIcon.classList.add('show');
    validationIcon.style.color = '#10B981';
  } else {
    input.classList.remove('valid');
    validationIcon.textContent = '✗';
    validationIcon.classList.add('show');
    validationIcon.style.color = '#DC2626';
  }
}

// ==========================================
// PROFILE COMPLETION CALCULATION
// ==========================================
function updateCompletion() {
  const matricNumber = document.getElementById('matricNumber').value;
  const department = document.getElementById('department').value;
  const level = document.getElementById('level').value;
  const bio = document.getElementById('bio').value;
  
  let completed = 0;
  const totalFields = 5;
  
  // Matric number (20%)
  if (matricNumber && /^[A-Z]{2,4}\/\d{4}\/\d{3}$/.test(matricNumber)) {
    completed += 1;
  }
  
  // Department (20%)
  if (department) {
    completed += 1;
  }
  
  // Level (20%)
  if (level) {
    completed += 1;
  }
  
  // Skills (20%)
  if (selectedSkills.size > 0 || customTags.size > 0) {
    completed += 1;
  }
  
  // Bio (20%)
  if (bio.length >= 50) {
    completed += 1;
  }
  
  const percentage = Math.round((completed / totalFields) * 100);
  
  // Update both completion indicators
  updateCompletionIndicator(percentage);
  updateCompletionSummary(percentage);
}

function updateCompletionIndicator(percentage) {
  const percentageElement = document.getElementById('completionPercentage');
  const progressBar = document.getElementById('completionProgress');
  
  percentageElement.textContent = percentage + '%';
  progressBar.style.width = percentage + '%';
}

function updateCompletionSummary(percentage) {
  const summaryValue = document.getElementById('completionSummary');
  const summaryProgress = document.getElementById('completionSummaryProgress');
  
  let strengthText = 'Getting Started';
  
  if (percentage >= 80) {
    strengthText = 'Excellent';
  } else if (percentage >= 60) {
    strengthText = 'Strong';
  } else if (percentage >= 40) {
    strengthText = 'Good Progress';
  } else if (percentage >= 20) {
    strengthText = 'Keep Going';
  }
  
  summaryValue.textContent = strengthText;
  summaryProgress.style.width = percentage + '%';
}

// ==========================================
// SKILL MANAGEMENT
// ==========================================
function toggleSkill(button) {
  const skill = button.getAttribute('data-skill');
  
  if (button.classList.contains('active')) {
    // Deselect skill
    button.classList.remove('active');
    selectedSkills.delete(skill);
  } else {
    // Select skill
    button.classList.add('active');
    selectedSkills.add(skill);
  }
  
  renderSelectedSkills();
  updateCompletion();
  updateSkillsInput();
}

function renderSelectedSkills() {
  const container = document.getElementById('selectedSkills');
  
  if (selectedSkills.size === 0 && customTags.size === 0) {
    container.innerHTML = '';
    return;
  }
  
  const allSkills = [...selectedSkills, ...customTags];
  
  container.innerHTML = allSkills.map(skill => `
    <div class="selected-skill">
      <span>${skill}</span>
      <button type="button" class="remove-skill" onclick="removeSelectedSkill('${skill}')" aria-label="Remove ${skill}">×</button>
    </div>
  `).join('');
}

function removeSelectedSkill(skill) {
  // Remove from selected skills
  if (selectedSkills.has(skill)) {
    selectedSkills.delete(skill);
    
    // Deactivate the button
    const buttons = document.querySelectorAll('.skill-chip');
    buttons.forEach(button => {
      if (button.getAttribute('data-skill') === skill) {
        button.classList.remove('active');
      }
    });
  }
  
  // Remove from custom tags
  if (customTags.has(skill)) {
    customTags.delete(skill);
  }
  
  renderSelectedSkills();
  updateCompletion();
}

// ==========================================
// TAG INPUT (ADDITIONAL SKILLS)
// ==========================================
function handleTagInput(event) {
  if (event.key === 'Enter') {
    event.preventDefault();
    
    const input = event.target;
    const value = input.value.trim();
    
    if (value.length === 0) return;
    
    // Check if skill already exists
    if (selectedSkills.has(value) || customTags.has(value)) {
      showNotification('This skill is already added', 'warning');
      input.value = '';
      return;
    }
    
    // Limit to 10 custom tags
    if (customTags.size >= 10) {
      showNotification('Maximum 10 additional skills allowed', 'warning');
      input.value = '';
      return;
    }
    
    // Add custom tag
    customTags.add(value);
    input.value = '';
    
    renderSelectedSkills();
    updateCompletion();
  }
}

// ==========================================
// CHARACTER COUNT
// ==========================================
function updateCharCount() {
  const bio = document.getElementById('bio');
  const charCount = document.getElementById('charCount');
  const current = bio.value.length;
  const max = 500;
  
  charCount.textContent = `${current} / ${max}`;
  
  if (current > max * 0.9) {
    charCount.style.color = '#DC2626';
  } else if (current > max * 0.7) {
    charCount.style.color = '#D97706';
  } else {
    charCount.style.color = '#9CA3AF';
  }
}

// ==========================================
// FORM SUBMISSION
// ==========================================
function handleSubmit(event) {

    const matricNumber =
        document.getElementById('matricNumber').value;

    const department =
        document.getElementById('department').value;

    const level =
        document.getElementById('level').value;

    const matricPattern = /^[A-Z0-9]+$/;
    console.log("Matric:", matricNumber);
    console.log("Regex Result:", matricPattern.test(matricNumber));

    if (!matricPattern.test(matricNumber)) {

        event.preventDefault();

        showNotification(
            'Please enter a valid matric number',
            'error'
        );

        return false;
    }

    if (!department || !level) {

        event.preventDefault();

        showNotification(
            'Please fill all required fields',
            'error'
        );

        return false;
    }

    updateSkillsInput();

    return true;
}
// ==========================================
// NOTIFICATION SYSTEM
// ==========================================
function showNotification(message, type = 'info') {
  // Remove existing notifications
  const existing = document.querySelector('.notification');
  if (existing) {
    existing.remove();
  }
  
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  
  const colors = {
    success: '#10B981',
    error: '#DC2626',
    warning: '#D97706',
    info: '#6366F1'
  };
  
  const icons = {
    success: '✓',
    error: '✗',
    warning: '⚠',
    info: 'ℹ'
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
    animation: slideInRight 0.3s ease-out;
    max-width: 400px;
  `;
  
  notification.innerHTML = `
    <span style="font-size: 20px;">${icons[type]}</span>
    <span>${message}</span>
  `;
  
  document.body.appendChild(notification);
  
  // Auto-remove after 4 seconds
  setTimeout(() => {
    notification.style.animation = 'slideOutRight 0.3s ease-out';
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 4000);
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      opacity: 0;
      transform: translateX(100px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
  
  @keyframes slideOutRight {
    from {
      opacity: 1;
      transform: translateX(0);
    }
    to {
      opacity: 0;
      transform: translateX(100px);
    }
  }
`;
document.head.appendChild(style);

// ==========================================
// HEADER ACTIONS
// ==========================================
function skipProfile() {
  if (confirm('Are you sure you want to skip profile setup? You can complete it later from your dashboard.')) {
    console.log('Skipping profile setup...');
    // window.location.href = '/dashboard';
    showNotification('You can complete your profile anytime from settings', 'info');
  }
}

function logout() {
  if (confirm('Are you sure you want to logout?')) {
    console.log('Logging out...');
    // window.location.href = '/login';
    showNotification('Logging out...', 'info');
  }
}

// ==========================================
// KEYBOARD SHORTCUTS
// ==========================================
document.addEventListener('keydown', (e) => {
  // Ctrl/Cmd + S = Save profile
  if ((e.ctrlKey || e.metaKey) && e.key === 's') {
    e.preventDefault();
    document.getElementById('profileForm').dispatchEvent(new Event('submit'));
  }
});

// ==========================================
// AUTO-SAVE DRAFT (OPTIONAL)
// ==========================================
let autoSaveTimeout;

function autoSaveDraft() {
  clearTimeout(autoSaveTimeout);
  
  autoSaveTimeout = setTimeout(() => {
    const formData = {
      matricNumber: document.getElementById('matricNumber').value,
      department: document.getElementById('department').value,
      level: document.getElementById('level').value,
      skills: [...selectedSkills, ...customTags],
      bio: document.getElementById('bio').value
    };
    
    // Save to localStorage
    localStorage.setItem('profileDraft', JSON.stringify(formData));
    console.log('Draft auto-saved');
  }, 2000);
}

// Load draft on page load
window.addEventListener('load', () => {
  const draft = localStorage.getItem('profileDraft');
  
  if (draft) {
    try {
      const data = JSON.parse(draft);
      
      if (data.matricNumber) document.getElementById('matricNumber').value = data.matricNumber;
      if (data.department) {
        document.getElementById('department').value = data.department;
        document.getElementById('department').classList.add('has-value');
      }
      if (data.level) {
        document.getElementById('level').value = data.level;
        document.getElementById('level').classList.add('has-value');
      }
      if (data.bio) document.getElementById('bio').value = data.bio;
      
      // Restore skills
      if (data.skills && data.skills.length > 0) {
        data.skills.forEach(skill => {
          // Check if it's a predefined skill
          const button = document.querySelector(`[data-skill="${skill}"]`);
          if (button) {
            button.classList.add('active');
            selectedSkills.add(skill);
          } else {
            // It's a custom tag
            customTags.add(skill);
          }
        });
        renderSelectedSkills();
      }
      
      updateCompletion();
      updateCharCount();
      
      console.log('Draft loaded from localStorage');
    } catch (e) {
      console.error('Error loading draft:', e);
    }
  }
});

// Attach auto-save to inputs
document.querySelectorAll('input, select, textarea').forEach(element => {
  element.addEventListener('input', autoSaveDraft);
  element.addEventListener('change', autoSaveDraft);
});

// ==========================================
// FORM FIELD INTERACTIONS
// ==========================================
// Add smooth scroll to first error
function scrollToError() {
  const firstError = document.querySelector('.form-input:invalid');
  if (firstError) {
    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    firstError.focus();
  }
}

function updateSkillsInput() {

    const ids = [];

    document.querySelectorAll('.skill-chip.active')
        .forEach(skill => {
            ids.push(skill.dataset.id);
        });

    document.getElementById('skillsInput').value =
        JSON.stringify(ids);
}
// Enhance select dropdowns
document.querySelectorAll('.form-select').forEach(select => {
  select.addEventListener('change', function() {
    if (this.value) {
      this.classList.add('has-value');
    } else {
      this.classList.remove('has-value');
    }
  });
});
