## README

#### Description
Beta release for WebQuiz, an online testing solution. 
This version supports the following features:
- [Identification for Student/Instructor](docs/identification.md)
- [Student Home Page](docs/student_home.md)
- [Instructor Home Page](docs/instructor_home.md)
- [Viewing the Question Bank](docs/view_bank.md)
- [Adding to the Question Bank](docs/add_bank.md)
- [Creating an Exam](docs/create_exam.md)
- [Taking an Exam](docs/take_exam.md)
- [Grading an Exam](docs/grade_exam.md)
- [Reviewing an Exam Result](docs/review_exam.md)


#### Endpoints
- `Front End` - responsible for handling the views (and logic) displayed to the user.
- `Middle End` - responsible for communicating information between Front and Back, and grading exams automatically.
- `Back End` - responsible for fetching and storing data from the database.

#### Cookies
Cookies are used on the site to maintain information between webpages.
Here's a list of the cookies that are used:

**userType**, active until log out/close window across entire site.
**dbID**, active until log out/close window across entire site.
**userName**, active until log out/close window across entire site.
**activeReviewExam**, active until log out/close window/leave exam review across entire site.
**activeClassID**, active until log out/close window/return to home page.