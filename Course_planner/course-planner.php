<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="course-planner-styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Course Planner</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../Login_register/user.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Course Search</h2>
                <div class="search-input-container">
                    <input type="text" id="search" class="form-control" placeholder="Search for a course...">
                    <button id="search-button" class="btn btn-primary">Search</button>
                </div>
                <div id="results" class="list-group results-container mt-3"></div>
            </div>
            <div class="col">
                <h2>My Schedule</h2>
                <div class="schedule-container">
                    <table id="schedule" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                            </tr>
                        </thead>
                        <tbody>
                            <script>
                                const startTime = 8 * 60;
                                const endTime = 18 * 60;
                                const interval = 30;
                                
                                for (let time = startTime; time <= endTime; time += interval) {
                                    const hour = Math.floor(time / 60);
                                    const minute = time % 60;
                                    const timeLabel = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                                    
                                    document.write(`
                                        <tr>
                                            <td>${timeLabel}</td>
                                            <td data-day="Monday" data-time="${time}"></td>
                                            <td data-day="Tuesday" data-time="${time}"></td>
                                            <td data-day="Wednesday" data-time="${time}"></td>
                                            <td data-day="Thursday" data-time="${time}"></td>
                                            <td data-day="Friday" data-time="${time}"></td>
                                        </tr>
                                    `);
                                }
                            </script>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function timeStringToMinutes(time) {
            const [hour, minute] = time.split(':');
            return parseInt(hour) * 60 + parseInt(minute);
        }

        function addCourseToSchedule(course) {
            const days = {
                'M': 'Monday',
                'T': 'Tuesday',
                'W': 'Wednesday',
                'R': 'Thursday',
                'F': 'Friday'
            };

            for (let dayChar of course.days) {
                let day = days[dayChar];
                const startTime = timeStringToMinutes(course.start_time);
                const endTime = timeStringToMinutes(course.end_time);

                for (let time = startTime; time < endTime; time += interval) {
                    const cell = $(`#schedule td[data-day="${day}"][data-time="${time}"]`);
                    if (cell.data('course')) {
                        alert('There is a time conflict with another course in your schedule. Please choose a different course.');
                        return;
                    }
                }
            }

            for (let dayChar of course.days) {
                let day = days[dayChar];
                const startTime = timeStringToMinutes(course.start_time);
                const endTime = timeStringToMinutes(course.end_time);

                for (let time = startTime; time < endTime; time += interval) {
                    const cell = $(`#schedule td[data-day="${day}"][data-time="${time}"]`);
                    cell.data('course', course);
                    cell.text(course.course_code);
                    cell.addClass('table-primary');

                    if (time === startTime) {
                        const durationInIntervals = Math.ceil((endTime - startTime) / interval);
                        cell.attr('rowspan', durationInIntervals);
                        cell.css('vertical-align', 'middle');
                        cell.on('click', function () {
                            if (confirm('Do you want to remove this course from your schedule?')) {
                                removeCourseFromSchedule(course);
                            }
                        });
                    } else {
                        cell.css('display', 'none');
                    }
                }
            }
        }

        function removeCourseFromSchedule(course) {
            const days = {
                'M': 'Monday',
                'T': 'Tuesday',
                'W': 'Wednesday',
                'R': 'Thursday',
                'F': 'Friday'
            };

            for (let dayChar of course.days) {
                let day = days[dayChar];
                const startTime = timeStringToMinutes(course.start_time);
                const endTime = timeStringToMinutes(course.end_time);

                for (let time = startTime; time < endTime; time += interval) {
                    const cell = $(`#schedule td[data-day="${day}"][data-time="${time}"]`);
                    cell.removeData('course');
                    cell.text('');
                    cell.removeClass('table-primary');
                    cell.removeAttr('rowspan');
                    cell.css('vertical-align', '');
                    cell.off('click');

                    if (time !== startTime) {
                        cell.css('display', '');
                    }
                }
            }

            // Show the cells for overlapping courses
            for (const otherCourse of selectedCourses) {
                if (otherCourse.code !== course.code) {
                    addCourseToSchedule(otherCourse);
                }
            }

            // Remove the course
            selectedCourses = selectedCourses.filter(c => c.code !== course.code);
        }


        function searchCourses() {
            let keyword = $('#search').val();
            let searchResults = $('#results');
            searchResults.empty();
            
            if (keyword.length < 3) {
                // Show all courses if the keyword is too short
                keyword = '';
            }
            
            $.getJSON('search.php', { keyword: keyword }, function(data) {
                for (let course of data) {
                    let resultItem = $('<div class="list-group-item list-group-item-action"></div>').text(course.course_code + ' - ' + course.course_name);
                    resultItem.click(() => {
                        addCourseToSchedule(course);
                    });
                    searchResults.append(resultItem);
                }
            });
        }

        //  Display all courses at the beginning
        searchCourses();

        // Conect the search button click event to the searchCourses function
        $('#search-button').on('click', searchCourses);

        $('#search').on('keyup', function (event) {
            if (event.which === 13) {
                // Enter key pressed, trigger search
                searchCourses();
            } else {
                // Other keys pressed, update search results
                searchCourses();
            }
        });

        $.getJSON('search.php', { keyword: keyword })
            .done(function(data) {
                // Success
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                // Error
                console.error('Error loading data:', textStatus, errorThrown);
            });

    </script>
</body>
</html>
