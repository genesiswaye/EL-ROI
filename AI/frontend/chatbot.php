<?php
session_start();
require_once "../../config/database.php";

if (

    !isset($_SESSION["user_id"])

) {

    header(

        "Location: ../../auth/login.php"

    );

    exit();
}

$userId =

    (int)

    $_SESSION["user_id"];

$stmt =

    $pdo->prepare(

        "

SELECT

full_name,
role

FROM users

WHERE id=?

LIMIT 1

"

    );

$stmt->execute(

    [$userId]

);

$user =

    $stmt->fetch(

        PDO::FETCH_ASSOC

    );

if (

    !$user

) {

    session_destroy();

    header(

        "Location: ../../auth/login.php"

    );

    exit();
}

$name =

    $user["full_name"];

$role =

    $user["role"];


$words =

    preg_split(

        '/\s+/',

        trim($name)

    );

$initials = "";

foreach (

    $words as $word

) {

    $initials .=

        mb_strtoupper(

            mb_substr(

                $word,
                0,
                1

            )

        );

    if (

        mb_strlen(

            $initials

        ) >= 2

    ) {

        break;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StudentLancer AI Assistant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="chatbot.css" />
</head>

<body>

    <div class="app">

        <!-- ══ LEFT SIDEBAR ══ -->
        <aside class="sidebar">

            <div class="sidebar-logo" style="display: flex; align-items: center; justify-content: center; ">
                <div class="logo-ico">
                    <img src="../../assets/studentLancerlogo.jpg" alt="Logo for website" width="38" height="38">
                </div>

            </div>

            <!-- <div class="sidebar-top">
                <button onclick="createNewChat()" class="new-chat-btn" onclick="clearChat()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M12 5V19M5 12H19" stroke="white" stroke-width="2.5" stroke-linecap="round" />
                    </svg>
                    New Chat
                </button>
            </div> -->

            <div class="sidebar-scroll">

                <!-- <div class="sidebar-section">
                    <div class="section-label">Recent Conversations</div>
                    <ul class="conv-list" id="conversationList">
                    </ul>
                </div> -->

                <div class="sidebar-section">
                    <div class="section-label">Suggested Questions</div>
                    <ul class="sugg-list">
                        <li class="sugg-item" onclick="sendSuggestion('How does escrow work on StudentLancer?')">
                            <span class="sugg-arrow">›</span> How does escrow work?
                        </li>
                        <li class="sugg-item" onclick="sendSuggestion('What wallet features does StudentLancer offer?')">
                            <span class="sugg-arrow">›</span> Show wallet features
                        </li>
                        <li class="sugg-item" onclick="sendSuggestion('How does the dispute resolution process work?')">
                            <span class="sugg-arrow">›</span> Explain disputes
                        </li>
                        <li class="sugg-item" onclick="sendSuggestion('How can I improve my AI Match Score?')">
                            <span class="sugg-arrow">›</span> Improve AI Match Score
                        </li>
                        <li class="sugg-item" onclick="sendSuggestion('How do withdrawals work on StudentLancer?')">
                            <span class="sugg-arrow">›</span> How do withdrawals work?
                        </li>
                    </ul>
                </div>

            </div>
            <a
                href="../../dashboard/overview.php"
                class="back-btn">

                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2">

                    <path d="M19 12H5"></path>

                    <path d="M12 19L5 12L12 5"></path>

                </svg>

                <span>

                    Back to Dashboard

                </span>

            </a>

            <div class="sidebar-profile">
                <div class="profile-avatar"><?= htmlspecialchars($initials) ?></div>
                <div class="profile-info">
                    <div class="profile-name"><?= htmlspecialchars($name) ?></div>
                    <div class="profile-role"><?= htmlspecialchars($role) ?></div>
                </div>
                <button class="profile-settings-btn" title="Settings">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="2" />
                    </svg>
                </button>
            </div>

        </aside>

        <!-- ══ MAIN CHAT ══ -->
        <main class="chat-main">

            <header class="chat-header">
                <div class="header-left">
                    <div class="header-avata">
                        <a href="../../dashboard/overview.php">
                            <img src="../../assets/studentLancerlogo.jpg" alt="Logo for website" width="48" height="48" viewBox="0 0 24 24" fill="none">
                        </a>
                    </div>
                    <div class="header-meta">
                        <div class="header-title-row">
                            <h1 class="header-title">StudentLancer AI</h1>
                            <div class="status-badge">
                                <span class="status-dot"></span>
                                <span class="status-label">Online</span>
                            </div>
                        </div>
                        <p class="header-subtitle">Ask about jobs, wallet, escrow, disputes, AI recommendations, payments, and platform workflows.</p>
                    </div>
                </div>
                <div class="header-actions">
                    <!-- <button class="header-action-btn" title="Search Chat" onclick="alert('Search coming soon')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2" />
                            <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button> -->
                    <button class="header-action-btn" title="Clear Chat" onclick="clearChat()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                    <button class="header-action-btn" title="Expand" onclick="document.documentElement.requestFullscreen?.()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M8 3H5a2 2 0 00-2 2v3M21 8V5a2 2 0 00-2-2h-3M3 16v3a2 2 0 002 2h3M16 21h3a2 2 0 002-2v-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </header>

            <div class="chat-messages" id="chatMessages">
                <div class="empty-state" id="emptyState">
                    <div class="empty-glow"></div>
                    <div class="empty-icon-wrap">
                        <div class="empty-icon">
                            <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2L14.5 9H22L16 13.5L18.5 21L12 16.5L5.5 21L8 13.5L2 9H9.5L12 2Z" fill="white" />
                            </svg>
                        </div>
                        <div class="empty-icon-ring"></div>
                        <div class="empty-icon-ring ring-2"></div>
                    </div>
                    <h2 class="empty-title">Hi, I'm StudentLancer AI.</h2>
                    <p class="empty-subtitle">I can explain platform workflows, wallet payments, escrow protection, disputes,<br>AI recommendations, jobs, withdrawals, and account features.</p>
                    <div class="empty-chips-grid">
                        <button class="empty-chip" onclick="sendSuggestion('How does escrow work on StudentLancer?')"><span class="chip-icon">🔒</span><span>How does escrow work?</span></button>
                        <button class="empty-chip" onclick="sendSuggestion('What wallet features does StudentLancer offer?')"><span class="chip-icon">💳</span><span>Show wallet features</span></button>
                        <button class="empty-chip" onclick="sendSuggestion('How are AI recommendations calculated?')"><span class="chip-icon">🤖</span><span>AI Recommendations</span></button>
                        <button class="empty-chip" onclick="sendSuggestion('How do withdrawals work on StudentLancer?')"><span class="chip-icon">💸</span><span>How do withdrawals work?</span></button>
                        <button class="empty-chip" onclick="sendSuggestion('How does the dispute resolution process work?')"><span class="chip-icon">⚖️</span><span>Explain disputes</span></button>
                        <button class="empty-chip" onclick="sendSuggestion('How can I improve my AI Match Score?')"><span class="chip-icon">📈</span><span>Improve AI Score</span></button>
                    </div>
                </div>
            </div>

            <div class="chat-input-area">
                <div class="quick-chips-row">
                    <button class="quick-chip" onclick="sendSuggestion('What wallet features does StudentLancer offer?')">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <rect x="1" y="4" width="22" height="16" rx="2" stroke="currentColor" stroke-width="2" />
                            <path d="M1 10h22" stroke="currentColor" stroke-width="2" />
                        </svg>
                        Wallet
                    </button>
                    <button class="quick-chip" onclick="sendSuggestion('How does escrow work on StudentLancer?')">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="11" width="18" height="11" rx="2" stroke="currentColor" stroke-width="2" />
                            <path d="M7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Escrow
                    </button>
                    <button class="quick-chip" onclick="sendSuggestion('Show me available jobs matching my skills.')">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2" />
                            <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="2" />
                        </svg>
                        Jobs
                    </button>
                    <button class="quick-chip" onclick="sendSuggestion('How are AI recommendations calculated?')">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                            <path d="M12 1v4M12 19v4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Recommendations
                    </button>
                    <button class="quick-chip" onclick="sendSuggestion('How does the dispute resolution process work?')">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <path d="M18 20V10M12 20V4M6 20v-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Disputes
                    </button>
                    <button class="quick-chip" onclick="sendSuggestion('How do payments work on StudentLancer?')">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none">
                            <line x1="12" y1="1" x2="12" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        Payments
                    </button>
                </div>

                <div class="input-box" id="inputBox">
                    <textarea id="messageInput" class="message-input" placeholder="Ask StudentLancer AI anything..." rows="1" maxlength="500"></textarea>
                    <div class="input-right">
                        <span class="char-counter" id="charCounter">0/500</span>
                        <button class="send-btn" id="sendBtn" onclick="sendMessage()">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                                <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>

                <p class="input-footer">StudentLancer AI &nbsp;·&nbsp; <kbd>Enter</kbd> to send &nbsp;·&nbsp; <kbd>Shift+Enter</kbd> for new line</p>
            </div>

        </main>
    </div>

    <script>
        // let currentSession= localStorage.getItem("studentlancer_session")
        // const AI_RESPONSES = {
        //   'How does escrow work on StudentLancer?': `**Escrow on StudentLancer** keeps your payments locked and secure until work is verified complete.\n\n**How the process works:**\n- Client deposits funds into escrow before any work begins\n- Funds are held securely — neither party can access them mid-project\n- Freelancer delivers the agreed deliverables\n- Client reviews and approves, triggering payment release\n- **Auto-release** activates after 7 days if no dispute is raised\n\n**What escrow protects:**\n- Freelancers from non-payment after delivery\n- Clients from paying for incomplete or poor work\n\n> **Tip:** Always define clear deliverables in your project agreement before starting work to avoid disputes.`,
        //   'What wallet features does StudentLancer offer?': `Your **StudentLancer Wallet** is your complete financial hub on the platform.\n\n**Core features:**\n- Instant deposits via card, bank transfer, or mobile money\n- Multi-currency support — GHS, USD, EUR\n- Withdrawals to MTN MoMo, Vodafone Cash, AirtelTigo, or bank\n- Full transaction history with downloadable receipts\n- Escrow balance tracking in real time\n- Monthly earnings dashboard with visual breakdown\n\n\`\`\`\nMinimum withdrawal:  GHS 50\nProcessing time:     1–3 business days\nPlatform fee:        5% on completed jobs\n\`\`\`\n\n> Navigate to **Wallet → Overview** in your dashboard to manage all balances.`,
        //   'How does the dispute resolution process work?': `**Disputes** on StudentLancer go through a structured 3-step resolution process.\n\n**Step 1 — Direct Resolution (48 hours)**\nBoth parties are given 48 hours to negotiate and resolve the issue independently.\n\n**Step 2 — Mediation**\nIf unresolved, a StudentLancer mediator reviews all submitted evidence and proposes a fair resolution.\n\n**Step 3 — Arbitration**\nA senior review panel makes a final, binding decision based on the original project contract terms.\n\n**How to raise a dispute:**\n1. Go to **My Projects → [Project Name]**\n2. Click **Raise Dispute** in the project menu\n3. Submit your evidence — messages, files, deliverables\n4. Our team responds within **48 business hours**\n\n> Disputes can only be raised while escrow funds are still active and held.`,
        //   'How can I improve my AI Match Score?': `Your **AI Match Score** determines how prominently you appear in client job recommendations.\n\n**Highest-impact improvements:**\n- Complete your profile to 100% — unlocks 2× visibility\n- Upload 3 or more portfolio samples\n- Maintain a client rating of 4.8 stars or above\n- Respond to client messages within 2 hours\n- Deliver projects on time — late delivery reduces score by up to 15%\n- Stay active — apply to at least 3 jobs per week\n\n**Your score breakdown:**\n| Signal | Weight | Status |\n|--------|--------|--------|\n| Profile completeness | 25% | 78% |\n| Client ratings | 30% | 4.7 avg |\n| On-time delivery | 20% | 91% |\n| Activity recency | 15% | Active |\n| Portfolio quality | 10% | 2 samples |\n\n\`\`\`\nCurrent AI Score:  78 / 100\nTop 10% threshold: 90+\n\`\`\``,
        //   'How do withdrawals work on StudentLancer?': `**Withdrawals** from your wallet are simple and processed in 1–3 business days.\n\n**Step-by-step process:**\n1. Go to **Wallet → Withdraw Funds**\n2. Select your method: Bank Transfer, MTN MoMo, Vodafone Cash, or AirtelTigo\n3. Enter the amount (minimum GHS 50)\n4. Confirm with your 2FA PIN\n5. Track status under **Transaction History**\n\n**Important notes:**\n- Funds in **active escrow** cannot be withdrawn\n- Auto-released escrow funds available within 24 hours\n- Withdrawal fee: GHS 2 flat rate\n\n> Processing times may vary slightly during bank holidays.`,
        //   'How are AI recommendations calculated?': `The **StudentLancer AI Engine** scores and ranks you using multiple performance signals.\n\n**Signal breakdown:**\n- **Skills match** — alignment between your profile and job requirements\n- **Completion rate** — your on-time delivery history across all projects\n- **Client ratings** — aggregated review scores from verified clients\n- **Activity recency** — how frequently you engage with the platform\n- **Portfolio quality** — assessed by our AI portfolio analyzer\n- **Profile completeness** — a fully completed profile scores higher\n\n> The AI recalculates your score every 48 hours based on fresh platform activity.`,
        //   'Show me available jobs matching my skills.': `Based on your profile, here are your **top-matched jobs** right now:\n\n| Job Title | Budget | Match |\n|-----------|--------|-------|\n| UI/UX Designer | GHS 1,200 | 94% |\n| React Frontend Dev | GHS 2,000 | 89% |\n| Backend Developer | GHS 2,500 | 87% |\n| Data Analyst | GHS 900 | 81% |\n| Brand Identity | GHS 750 | 76% |\n\n> **Tip:** Jobs marked ⚡ are urgent and close within 24 hours — they pay a 10% urgency premium.`,
        //   'How do payments work on StudentLancer?': `**Payments on StudentLancer** are fully escrow-protected and processed securely.\n\n**Payment lifecycle:**\n1. Client creates a project and deposits payment into escrow\n2. You complete the agreed deliverables\n3. Client reviews the work and releases payment\n4. Funds arrive in your StudentLancer wallet instantly\n5. Withdraw to your bank or mobile money anytime\n\n**Fee structure:**\n\`\`\`\nPlatform fee:         5%\nWithdrawal fee:       GHS 2 flat\nCurrency conversion:  0.5% markup\n\`\`\`\n\n> All payment processing is PCI-DSS compliant.`,
        //   'Check the status of my job applications.': `Here's an overview of your **active job applications**:\n\n| Project | Client | Status | Applied |\n|---------|--------|--------|---------|\n| Brand Identity System | TechStartup GH | 🟡 Under Review | 2 days ago |\n| Mobile App Redesign | Fintech Ltd | 🟢 Shortlisted | 1 day ago |\n| Data Dashboard | Research Lab, UG | 🔵 Proposal Sent | 4 hours ago |\n| API Integration | StartupHub | 🔴 Not Selected | 5 days ago |\n\n> You have **2 applications** awaiting client review — average response time is 48 hours.`,
        // };

        // const DEFAULT_RESPONSE = `I'm here to help you navigate **StudentLancer** efficiently.\n\n**Here's what I can assist with:**\n- 💼 **Jobs** — finding, applying for, and managing projects\n- 💳 **Wallet & Payments** — deposits, withdrawals, transaction history\n- 🔒 **Escrow** — understanding payment protection and release\n- 🤖 **AI Recommendations** — boosting your profile match score\n- ⚖️ **Disputes** — raising and resolving project disagreements\n- 📈 **Profile Optimization** — tips to attract more clients\n\nWhat would you like to explore today?`;

        function formatTime(time = null) {

            if (!time) {

                return new Date().toLocaleTimeString(
                    [], {
                        hour: '2-digit',
                        minute: '2-digit'
                    }
                )

            }

            let cleanTime = time.replace('T', ' ')

            let date = new Date(cleanTime)

            return date.toLocaleTimeString(
                [], {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            )

        }

        function getTime() {
            return new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function parseInline(text) {
            return text
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                .replace(/`([^`]+)`/g, '<code class="i-code">$1</code>');
        }

        function renderMarkdown(raw) {
            const lines = raw.split('\n');
            let out = '',
                inUL = false,
                inOL = false,
                inCode = false,
                codeBuf = [],
                inTable = false,
                tableBuf = [];
            const closeUL = () => {
                if (inUL) {
                    out += '</ul>';
                    inUL = false;
                }
            };
            const closeOL = () => {
                if (inOL) {
                    out += '</ol>';
                    inOL = false;
                }
            };
            const closeTable = () => {
                if (!inTable) return;
                inTable = false;
                out += '<div class="tbl-wrap"><table class="ai-table">';
                tableBuf.forEach((row, i) => {
                    if (/^[\s|:-]+$/.test(row.replace(/\|/g, ''))) return;
                    const cells = row.split('|').filter(c => c.trim() !== '');
                    const tag = i === 0 ? 'th' : 'td';
                    out += '<tr>' + cells.map(c => `<${tag}>${parseInline(c.trim())}</${tag}>`).join('') + '</tr>';
                });
                out += '</table></div>';
                tableBuf = [];
            };
            for (let i = 0; i < lines.length; i++) {
                const line = lines[i];
                if (line.startsWith('```')) {
                    if (!inCode) {
                        closeUL();
                        closeOL();
                        closeTable();
                        inCode = true;
                        codeBuf = [];
                    } else {
                        inCode = false;
                        out += `<pre class="code-block"><code>${codeBuf.join('\n')}</code></pre>`;
                        codeBuf = [];
                    }
                    continue;
                }
                if (inCode) {
                    codeBuf.push(line.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'));
                    continue;
                }
                if (line.startsWith('|')) {
                    closeUL();
                    closeOL();
                    inTable = true;
                    tableBuf.push(line);
                    if (i + 1 >= lines.length || !lines[i + 1].startsWith('|')) closeTable();
                    continue;
                }
                if (!line.startsWith('- ') && !/^\d+\./.test(line)) {
                    closeUL();
                    closeOL();
                }
                if (!line.startsWith('|')) closeTable();
                if (line.startsWith('> ')) {
                    out += `<blockquote class="ai-quote">${parseInline(line.slice(2))}</blockquote>`;
                } else if (line.startsWith('- ')) {
                    if (!inUL) {
                        out += '<ul class="ai-ul">';
                        inUL = true;
                    }
                    out += `<li>${parseInline(line.slice(2))}</li>`;
                } else if (/^\d+\./.test(line)) {
                    if (!inOL) {
                        out += '<ol class="ai-ol">';
                        inOL = true;
                    }
                    out += `<li>${parseInline(line.replace(/^\d+\.\s/,''))}</li>`;
                } else if (line.trim() === '') {
                    out += '<div class="ai-gap"></div>';
                } else {
                    out += `<p class="ai-p">${parseInline(line)}</p>`;
                }
            }
            closeUL();
            closeOL();
            closeTable();
            return out;
        }

        function hideEmpty() {
            const el = document.getElementById('emptyState');
            if (el) el.remove();
        }

        function scrollBottom() {
            const c = document.getElementById('chatMessages');
            c.scrollTo({
                top: c.scrollHeight,
                behavior: 'smooth'
            });
        }

        function addUserMessage(text, time = null) {
            hideEmpty();
            const wrap = document.getElementById('chatMessages');
            const el = document.createElement('div');
            el.className = 'msg msg--user';
            el.innerHTML = `<div class="msg-bubble msg-bubble--user"><p>${text.replace(/</g,'&lt;')}</p></div><span class="msg-time">${formatTime(time)}</span>`;
            wrap.appendChild(el);
            scrollBottom();
        }

        function showTyping() {
            hideEmpty();
            const wrap = document.getElementById('chatMessages');
            const el = document.createElement('div');
            el.className = 'msg msg--ai';
            el.id = 'typingIndicator';
            el.innerHTML = `<div class="msg-avatar"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2L14.5 9H22L16 13.5L18.5 21L12 16.5L5.5 21L8 13.5L2 9H9.5L12 2Z" fill="white"/></svg></div><div class="msg-body"><div class="msg-sender">StudentLancer AI</div><div class="typing-bubble"><span class="typing-label">StudentLancer AI is thinking</span><span class="typing-dots"><span></span><span></span><span></span></span></div></div>`;
            wrap.appendChild(el);
            scrollBottom();
        }

        function removeTyping() {
            const el = document.getElementById('typingIndicator');
            if (el) el.remove();
        }

        function addAIMessage(content, time = null) {
            const wrap = document.getElementById('chatMessages');
            const el = document.createElement('div');
            el.className = 'msg msg--ai';
            el.innerHTML = `
    <div class="msg-avatar"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2L14.5 9H22L16 13.5L18.5 21L12 16.5L5.5 21L8 13.5L2 9H9.5L12 2Z" fill="white"/></svg></div>
    <div class="msg-body">
      <div class="msg-sender">StudentLancer AI</div>
      <div class="msg-bubble msg-bubble--ai">${renderMarkdown(content)}</div>
      <div class="msg-footer">
        <span class="msg-time">${formatTime(time)}</span>
        <div class="msg-actions">
          <button class="action-btn" onclick="copyMsg(this)"><svg width="13" height="13" viewBox="0 0 24 24" fill="none"><rect x="9" y="9" width="13" height="13" rx="2" stroke="currentColor" stroke-width="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" stroke="currentColor" stroke-width="2"/></svg> Copy</button>
          <button class="action-btn vote-btn" onclick="vote(this,'up')"><svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M14 9V5a3 3 0 00-3-3l-4 9v11h11.28a2 2 0 002-1.7l1.38-9a2 2 0 00-2-2.3H14z" stroke="currentColor" stroke-width="2"/><path d="M7 22H4a2 2 0 01-2-2v-7a2 2 0 012-2h3" stroke="currentColor" stroke-width="2"/></svg></button>
          <button class="action-btn vote-btn" onclick="vote(this,'down')"><svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M10 15v4a3 3 0 003 3l4-9V2H5.72a2 2 0 00-2 1.7l-1.38 9a2 2 0 002 2.3H10z" stroke="currentColor" stroke-width="2"/><path d="M17 2h2.67A2.31 2.31 0 0122 4v7a2.31 2.31 0 01-2.33 2H17" stroke="currentColor" stroke-width="2"/></svg></button>
        </div>
      </div>
    </div>`;
            wrap.appendChild(el);
            scrollBottom();
        }
        async function loadPreviousChat(text, time = null) {

            let response =

                await fetch(

                    "http://127.0.0.1:8000/chat-memory/<?= $_SESSION['user_id'] ?>"

                )

            let messages =

                await response.json()
            console.log(messages)

            messages.forEach(

                msg => {

                    if (

                        msg.role === "user"

                    ) {

                        addUserMessage(

                            msg.message,
                            msg.created_at

                        )

                    } else {

                        addAIMessage(

                            msg.message,
                            msg.created_at

                        )

                    }

                }

            )
        }
        // async function loadHistory() {

        //     let response =

        //         await fetch(

        //             "http://127.0.0.1:8000/chat-history/1"

        //         )

        //     let sessions =

        //         await response.json()

        //     renderHistory(sessions)

        // }
        // async function createNewChat() {

        //     clearChat()

        //     let response =

        //         await fetch(

        //             "http://127.0.0.1:8000/new-chat",

        //             {

        //                 method: "POST",

        //                 headers: {

        //                     "Content-Type":

        //                         "application/json"

        //                 },

        //                 body: JSON.stringify({

        //                     user_id: 1

        //                 })

        //             }

        //         )

        //     let data =

        //         await response.json()

        //     currentSession =

        //         data.session_id

        //     loadHistory()

        //     currentSession =

        //         data.session_id

        //     localStorage.setItem(

        //         "studentlancer_session",

        //         currentSession

        //     )

        // }

        async function sendMessage() {

            let input =
                document.getElementById(

                    "messageInput"

                )

            let text =
                input.value.trim()

            if (!text) return

            // if (

            //     currentSession == null

            // ) {

            //     await createNewChat()

            // }
            addUserMessage(text)

            input.value = ""

            showTyping()

            try {

                let response =

                    await fetch(

                        "http://127.0.0.1:8000/chat",

                        {

                            method: "POST",

                            headers: {

                                "Content-Type":

                                    "application/json"

                            },
                            body: JSON.stringify({

                                user_id: <?= $_SESSION['user_id'] ?>,

                                // session_id:

                                // currentSession,

                                message: text

                            })
                        }

                    )

                console.log(

                    "HTTP STATUS:",

                    response.status

                )

                let data =

                    await response.json()

                console.log(

                    "SERVER DATA:",

                    data

                )

                removeTyping()

                addAIMessage(

                    data.reply ||

                    "No response received."

                )
                console.log(data)

            } catch (error) {

                removeTyping()

                console.log(

                    "FETCH ERROR:",

                    error

                )

                addAIMessage(

                    "Server unavailable."

                )

            }

        }

        function sendSuggestion(text) {

            document
                .getElementById(
                    "messageInput"
                )
                .value = text

            sendMessage()

        }

        // function renderHistory(

        //     sessions

        // ) {

        //     let html = ""

        //     sessions.forEach(

        //         session => {

        //             html += `

        //         <li

        //         class="conv-item"

        //         onclick="loadConv(

        //         this,

        //         ${session.id}

        //         )">

        //         <svg class="conv-icon"
        //         width="13"
        //         height="13">

        //         ...

        //         </svg>

        //         <span
        //         class="conv-title">

        //         ${session.title}

        //         </span>

        //         </li>

        //         `

        //         }

        //     )

        //     document
        //         .getElementById(

        //             "conversationList"

        //         )
        //         .innerHTML =

        //         html

        // }

        // async function loadConv(

        //     el,
        //     sessionId

        // ) {

        //     document
        //         .querySelectorAll(

        //             '.conv-item'

        //         )
        //         .forEach(

        //             i => i.classList.remove(

        //                 'active'

        //             )

        //         )

        //     el.classList.add(

        //         'active'

        //     )

        //     let response =

        //         await fetch(

        //             `http://127.0.0.1:8000/chat-session/${sessionId}`

        //         )

        //     let messages =

        //         await response.json()

        //     let chat =

        //         document.getElementById(

        //             'chatMessages'

        //         )

        //     chat.innerHTML = ''

        //     messages.forEach(

        //         msg => {

        //             if (

        //                 msg.role === "user"

        //             ) {

        //                 addUserMessage(

        //                     msg.message

        //                 )

        //             } else {

        //                 addAIMessage(

        //                     msg.message

        //                 )

        //             }

        //         }

        //     )

        // }

        function clearChat() {
            document.getElementById('chatMessages').innerHTML = `<div class="empty-state" id="emptyState"><div class="empty-glow"></div><div class="empty-icon-wrap"><div class="empty-icon"><svg width="38" height="38" viewBox="0 0 24 24" fill="none"><path d="M12 2L14.5 9H22L16 13.5L18.5 21L12 16.5L5.5 21L8 13.5L2 9H9.5L12 2Z" fill="white"/></svg></div><div class="empty-icon-ring"></div><div class="empty-icon-ring ring-2"></div></div><h2 class="empty-title">Hi, I'm StudentLancer AI.</h2><p class="empty-subtitle">I can explain platform workflows, wallet payments, escrow protection, disputes,<br>AI recommendations, jobs, withdrawals, and account features.</p><div class="empty-chips-grid"><button class="empty-chip" onclick="sendSuggestion('How does escrow work on StudentLancer?')"><span class="chip-icon">🔒</span><span>How does escrow work?</span></button><button class="empty-chip" onclick="sendSuggestion('What wallet features does StudentLancer offer?')"><span class="chip-icon">💳</span><span>Show wallet features</span></button><button class="empty-chip" onclick="sendSuggestion('How are AI recommendations calculated?')"><span class="chip-icon">🤖</span><span>AI Recommendations</span></button><button class="empty-chip" onclick="sendSuggestion('How do withdrawals work on StudentLancer?')"><span class="chip-icon">💸</span><span>How do withdrawals work?</span></button><button class="empty-chip" onclick="sendSuggestion('How does the dispute resolution process work?')"><span class="chip-icon">⚖️</span><span>Explain disputes</span></button><button class="empty-chip" onclick="sendSuggestion('How can I improve my AI Match Score?')"><span class="chip-icon">📈</span><span>Improve AI Score</span></button></div></div>`;
        }

        function copyMsg(btn) {
            const bubble = btn.closest('.msg-body').querySelector('.msg-bubble--ai');
            navigator.clipboard.writeText(bubble.innerText).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none"><polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg> Copied!`;
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.innerHTML = orig;
                    btn.classList.remove('copied');
                }, 2000);
            });
        }

        function regenMsg(btn) {
            const bubble = btn.closest('.msg-body').querySelector('.msg-bubble--ai');
            bubble.style.opacity = '0.4';
            setTimeout(() => {
                bubble.innerHTML = renderMarkdown(DEFAULT_RESPONSE);
                bubble.style.opacity = '1';
            }, 900);
        }

        function vote(btn, dir) {
            btn.parentElement.querySelectorAll('.vote-btn').forEach(b => b.classList.remove('voted-up', 'voted-down'));
            btn.classList.add(dir === 'up' ? 'voted-up' : 'voted-down');
        }

        function updateCounter() {
            const input = document.getElementById('messageInput');
            const counter = document.getElementById('charCounter');
            const n = input.value.length;
            counter.textContent = `${n}/500`;
            counter.style.color = n > 450 ? '#f59e0b' : n > 490 ? '#ef4444' : 'rgba(255,255,255,0.25)';
        }

        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 130) + 'px';
        }

        document.addEventListener(

            "DOMContentLoaded",

            () => {


                input.addEventListener(

                    "input",

                    () => {

                        updateCounter()

                        autoResize(

                            input

                        )

                    }

                )

                input.addEventListener(

                    "keydown",

                    e => {

                        if (

                            e.key === "Enter"

                            &&

                            !e.shiftKey

                        ) {

                            e.preventDefault()

                            sendMessage()

                        }

                    }

                )

            }
        )
        document.addEventListener(

            "DOMContentLoaded",

            () => {

                loadPreviousChat()

            })

        console.log(

            "CURRENT USER:",

            <?= $_SESSION['user_id'] ?>

        )
    </script>
</body>

</html>