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
                <div class="tags mt-2">
                    <button class="btn btn-outline-secondary btn-sm" data-tag="math">Math</button>
                    <button class="btn btn-outline-secondary btn-sm" data-tag="morning">Morning</button>
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

    <div class="row mt-4">
        <div class="col">
            <h2>Add My Event</h2>
            <form id="custom-event-form">
                <div class="mb-3">
                    <label for="event-name" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="event-name" required>
                </div>
                <div class="mb-3">
                    <label for="start-time" class="form-label">Start Time (HH:mm)</label>
                    <input type="text" class="form-control" id="start-time" pattern="^([0-1][0-9]|2[0-3]):[0-5][0-9]$" required>
                </div>
                <div class="mb-3">
                    <label for="end-time" class="form-label">End Time (HH:mm)</label>
                    <input type="text" class="form-control" id="end-time" pattern="^([0-1][0-9]|2[0-3]):[0-5][0-9]$" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Days</label>
                    <div id="custom-event-days" class="d-flex">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="custom-event-day-m" value="M">
                            <label class="form-check-label" for="custom-event-day-m">Monday</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="custom-event-day-t" value="T">
                            <label class="form-check-label" for="custom-event-day-t">Tuesday</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="custom-event-day-w" value="W">
                            <label class="form-check-label" for="custom-event-day-w">Wednesday</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="custom-event-day-r" value="R">
                            <label class="form-check-label" for="custom-event-day-r">Thursday</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="custom-event-day-f" value="F">
                            <label class="form-check-label" for="custom-event-day-f">Friday</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Add Event</button>
            </form>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        function filterCoursesByTags(tags) {
            let filteredCourses = allCourses;

            if (tags.includes('math')) {
                filteredCourses = filteredCourses.filter(course => course.course_code.toLowerCase().includes('math'));
            }

            if (tags.includes('morning')) {
                filteredCourses = filteredCourses.filter(course => parseInt(course.start_time.split(':')[0]) < 12);
            }

            displayCourses(filteredCourses);
        }

        function displayCourses(courses) {
            let searchResults = $('#results');
            searchResults.empty();

            for (let course of courses) {
                let resultItem = $('<div class="list-group-item list-group-item-action"></div>').text(course.course_code + ' - ' + course.course_name);
                resultItem.click(() => {
                    addCourseToSchedule(course);
                });
                searchResults.append(resultItem);
            }
        }

        function fetchCourses() {
            $.getJSON('search.php', function(data) {
                allCourses = data;
                displayCourses(allCourses);
            });
        }

        // Handle tag click events
        const activeTags = new Set();
        $('.tags button').on('click', function() {
            const tag = $(this).data('tag');

            if (activeTags.has(tag)) {
                activeTags.delete(tag);
                $(this).removeClass('btn-primary').addClass('btn-outline-secondary');
            } else {
                activeTags.add(tag);
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
            }

            filterCoursesByTags(Array.from(activeTags));
        });

        let allCourses = [];
        fetchCourses();

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

            // Get the selected tags
            const selectedTags = $('.btn-outline-primary.active').map(function() {
                return $(this).data('tag');
            }).get();

            // Add an optional filter to the search.php request based on the selected tags
            const tagFilter = selectedTags.length > 0 ? { tags: selectedTags.join(',') } : {};

            $.getJSON('search.php', { keyword: keyword, ...tagFilter }, function(data) {
                for (let course of data) {
                    let resultItem = $('<div class="list-group-item list-group-item-action"></div>').text(course.course_code + ' - ' + course.course_name);
                    resultItem.click(() => {
                        addCourseToSchedule(course);
                    });
                    searchResults.append(resultItem);
                }
            });
        }


        function addCustomEventToSchedule(event) {
            const customEvent = {
                code: 'custom_' + Date.now(),
                course_code: event.name,
                start_time: event.start_time,
                end_time: event.end_time,
                days: event.days.join('')
            };
            addCourseToSchedule(customEvent);
        }

        $('#custom-event-form').on('submit', function (event) {
            event.preventDefault();

            const eventName = $('#event-name').val().trim();
            const startTime = $('#start-time').val().trim();
            const endTime = $('#end-time').val().trim();
            const days = $('#custom-event-days input:checked').map(function () {
                return this.value;
            }).get();

            if (eventName && startTime && endTime && days.length) {
                addCustomEventToSchedule({
                    name: eventName,
                    start_time: startTime,
                    end_time: endTime,
                    days: days
                });

                // Clear form inputs
                $('#event-name').val('');
                $('#start-time').val('');
                $('#end-time').val('');
                $('#custom-event-days input:checked').prop('checked', false);
            } else {
                alert('Please fill in all required fields and select at least one day.');
            }
        });

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
        
        // Toggle tag buttons and trigger search
        $('button[data-tag]').on('click', function() {
            $(this).toggleClass('active');
            searchCourses();
        });

    </script>
</body>
</html>
