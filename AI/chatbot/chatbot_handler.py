from chatbot.prompt import (
    SYSTEM_PROMPT
)

from chatbot.intents import (
    detect_intent
)

from database import (

    get_history,
    save_message,
    get_wallet_balance,
    get_completed_jobs,
    get_pending_withdrawals

)

import ollama


async def process_chat(
    user_id,
    message
):

    intent = detect_intent(
        message
    )


    # WALLET BALANCE

    if intent == "wallet_balance":

        balance = get_wallet_balance(
            user_id
        )

        reply = f"""

        Your wallet balance is:

        ₦{balance}

        """

        save_message(

                user_id,

                "user",

                message

            )

        save_message(

                user_id,

                "assistant",

                reply

            )

        return reply


    # COMPLETED JOBS

    if intent == "completed_jobs":

        jobs = get_completed_jobs(
            user_id
        )

    
        reply = f"""

        You have completed:

        {jobs} jobs.

        """

        save_message(

                user_id,

                "user",

                message

            )

        save_message(

                user_id,

                "assistant",

                reply

            )

        return reply


    # PENDING WITHDRAWALS

    if intent == "pending_withdrawals":

        pending = get_pending_withdrawals(
            user_id
        )

        reply = f"""

        You currently have:

        ₦{pending} in pending withdrawals.

        """

        save_message(

                user_id,

                "user",

                message

            )

        save_message(

                user_id,

                "assistant",

                reply

            )

        return reply
    


    if intent=="withdrawal_help":

        reply = """

        Withdrawal Process:

        1. Open Wallet

        2. Click Withdraw

        3. Enter amount

        4. Select bank account

        5. Submit request

        Funds move into withdrawal hold.

        Admins review requests.

        Approved:

        Money leaves platform.

        Rejected:

        Funds return to wallet.

        Minimum withdrawal:

        ₦5000

        Processing depends on admin approval.

        """

        save_message(

            user_id,

            "user",

            message

        )

        save_message(

            user_id,

            "assistant",

            reply

        )

        return reply

    # AI SECTION

    history = get_history(
        user_id
    )

    messages = [

        {

            "role":"system",

            "content":SYSTEM_PROMPT

        }

    ]

    messages.extend(
        history
    )

    messages.append(

        {

            "role":"user",

            "content":message

        }

    )
    import time

    start = time.time()
    try:

        response = ollama.chat(

            model="qwen2.5:1.5b",

            messages=messages,

            options={

                "num_predict":700,
                "temperature":0.5,
                "num_ctx":4096

            }

        )
        print(
        "OLLAMA TIME:",
        round(time.time() - start, 2),
        "seconds"
    )

        reply = response["message"]["content"]

        save_message(user_id, "user", message)

        save_message(

            user_id,
            "assistant",
            reply

        )

        return reply

    except Exception as e:

        print(

            "OLLAMA ERROR:",

            e

        )

        return (

            "AI temporarily unavailable."

        )