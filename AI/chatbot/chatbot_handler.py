from chatbot.memory import (
    get_history,
    save_message
)

from chatbot.prompt import (
    SYSTEM_PROMPT
)

from chatbot.intents import (
    detect_intent
)

from database import (

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

        return f"Your wallet balance is ₦{balance}"


    # COMPLETED JOBS

    if intent == "completed_jobs":

        jobs = get_completed_jobs(
            user_id
        )

        return f"You have completed {jobs} jobs."


    # PENDING WITHDRAWALS

    if intent == "pending_withdrawals":

        pending = get_pending_withdrawals(
            user_id
        )

        return f"You currently have ₦{pending} in pending withdrawals."


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

    response = ollama.chat(

        model="qwen2.5:1.5b",

        messages=messages,

        options={

            "num_predict":100,
            "temperature":0.5,
            "num_ctx":2048

        }

    )

    reply = response[
        "message"
    ][
        "content"
    ]

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