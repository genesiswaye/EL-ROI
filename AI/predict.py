import joblib
import sys
import pandas as pd
import os

# Load trained model
BASE_DIR = os.path.dirname(
os.path.abspath(__file__)
)

MODEL_PATH = os.path.join(

BASE_DIR,

"studentlancer_model.pkl"

)

model = joblib.load(
MODEL_PATH
)

# Receive data from PHP

skill_match=float(sys.argv[1])

matched_skills=int(sys.argv[2])

# total_required_skills=int(sys.argv[3])

rating=float(sys.argv[3])

completed_jobs=int(sys.argv[4])

# Predict probability

input_data = pd.DataFrame([{
"skill_match":skill_match,

"matched_skills":matched_skills,

# "total_required_skills":total_required_skills,

"rating":rating,

"completed_jobs":completed_jobs
}])

prediction = model.predict_proba(input_data)[0][1]

print(round(prediction * 100, 2))