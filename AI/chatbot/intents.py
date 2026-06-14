def detect_intent(message):

    message = message.lower()


    if (

    "wallet balance" in message

    or

    "my balance" in message

    ):

        return "wallet_balance"


    if (

    "wallet features" in message

    or

    "wallet offer" in message

    ):

        return "wallet_features"


    if (

    "completed jobs" in message

    ):

        return "completed_jobs"


    if (

    "pending withdrawal" in message

    ):

        return "pending_withdrawals"


    if (

    "withdraw" in message

    ):

        return "withdrawal_help"


    return "general"