import pandas as pd
import mysql.connector
import joblib

from sklearn.pipeline import Pipeline
from sklearn.preprocessing import StandardScaler
from sklearn.linear_model import LogisticRegression

# DATABASE CONNECTION

db = mysql.connector.connect(

host="localhost",

user="root",

password="",

database="finalyearproject_db"

)

query = """

SELECT

applications.id,

applications.status,

users.rating,

users.completed_jobs,

applications.student_id,

applications.job_id

FROM applications

JOIN jobs
ON applications.job_id=jobs.id

JOIN users
ON applications.student_id=users.id

LEFT JOIN student_skills
ON users.id=student_skills.user_id

GROUP BY
applications.id

"""

data = pd.read_sql(
query,
db
)

matched_skills = []

skill_match = []

for _, row in data.iterrows():

    student_id = row["student_id"]

    job_id = row["job_id"]

    student_query = """

    SELECT skill_id

    FROM student_skills

    WHERE user_id=%s

    """

    student_skills = pd.read_sql(

        student_query,

        db,

        params=[student_id]

    )

    job_query = """

    SELECT skill_id

    FROM job_skills

    WHERE job_id=%s

    """

    job_skills = pd.read_sql(

        job_query,

        db,

        params=[job_id]

    )

    student_set = set(
        student_skills["skill_id"]
    )

    job_set = set(
        job_skills["skill_id"]
    )

    matched = len(

        student_set

        &

        job_set

    )

    total = max(

        len(job_set),

        1

    )

    matched_skills.append(

        matched

    )

    skill_match.append(

        matched / total

    )

data["matched_skills"] = matched_skills

data["skill_match"] = skill_match
# FEATURE ENGINEERING

data["skill_match"] = (

data["matched_skills"]

/

3

)

data["hired"] = (
data["status"].isin([
"accepted",
"completed"
])
).astype(int)

X = data[[

"skill_match",

"matched_skills",

"rating",

"completed_jobs"

]]


# Target
y = data["hired"]

# Train model
model = LogisticRegression()
model = Pipeline([

("scaler",StandardScaler()),

("model",LogisticRegression())

])
model.fit(X, y)

# Save model
joblib.dump(model, "studentlancer_model.pkl")

print("AI model trained successfully!")