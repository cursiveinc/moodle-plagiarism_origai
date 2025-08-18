import * as config from 'core/config';

export const init = () => {
    /**
     * Starts polling given scan ID and element
     */
    const startPolling = (pollElement) => {
        const pollId = pollElement.getAttribute('data-plagiarism_origa-poll-scanid');
        const pollInterval = parseInt(pollElement.getAttribute('data-plagiarism_origa-poll-interval'), 10) || 5000;
        const cmid = pollElement.getAttribute('data-plagiarism_origa-cmid');
        const coursemodule = pollElement.getAttribute('data-plagiarism_origa-coursemodule');
        const sesskey = pollElement.getAttribute('data-plagiarism_origa-sesskey');

        const pollsearchparam = new URLSearchParams({
            scanid: pollId,
            returnurl: window.location.href,
            cmid,
            coursemodule,
            sesskey
        });

        const pollUrl = `${config.wwwroot}/plagiarism/origai/poll_scan.php?${pollsearchparam.toString()}`;

        const pollFunction = () => {
            fetch(pollUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                  if (data.status === 'completed') {
                      clearInterval(pollIntervalId);
                      pollElement.outerHTML = data.renderhtml;
                  }
              })
              .catch(error => {
                  console.error('Polling error:', error);
              });
        };

        const pollIntervalId = setInterval(pollFunction, pollInterval);
    };

    // Attach polling to static DOM
    setTimeout(() => {
        const pollActions = document.querySelectorAll('[data-plagiarism_origa-poll-scanid]');
        pollActions.forEach(pollAction => {
            startPolling(pollAction);
        });
    }, 500);

    // Click handler for triggering scan
    document.body.addEventListener('click', function(event) {
        const target = event.target.closest('[data-plagiarism_origa-trigger-scanid]');
        if (target && target.tagName == 'A') {
            event.preventDefault();
            if (!(target instanceof HTMLAnchorElement)) {
                console.warn("Target is not an anchor element");
                return;
            }

            const url = new URL(target.href);
            const cmid = url.searchParams.get('cmid');
            const coursemodule = url.searchParams.get('coursemodule');
            const sesskey = url.searchParams.get('sesskey');
            const returnurl = url.searchParams.get('returnurl');
            const scantype = url.searchParams.get('scantype');
            const scanId = target.getAttribute('data-plagiarism_origa-trigger-scanid');

            const scanUrl = `${config.wwwroot}/plagiarism/origai/scan_content.php`;
            const bodyParams = {
                scanid: scanId,
                isasync: 1,
                cmid,
                coursemodule,
                sesskey,
                returnurl
            };

            fetch(scanUrl + '?' + new URLSearchParams(bodyParams), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(bodyParams)
            }).then(response => response.json())
            .then(data => {
                target.outerHTML = data.renderhtml;

                if (data.status === 'success') {
                    // Wait for DOM to update, then find the new poll element and start polling
                    requestAnimationFrame(() => {
                        const newPollEl = document.querySelector(`[data-plagiarism_origa-poll-scanid="${scanId}"]`);
                        if (newPollEl) {
                            startPolling(newPollEl);
                        } else {
                            if (config.display) {
                                console.warn('No poll element found after scan trigger for scanId:', scanId);
                            }
                        }
                    });
                }
                return;
            })
            .catch(() => {
                target.outerHTML = `
                    <div class="origai-section">
                        <h3 class="origai-section-title">${scantype === 'ai' ? 'AI Check' : 'Plagiarism Check'}</h3>
                        <div class="d-flex align-items-center text-danger">
                            <i class="fa fa-exclamation-circle me-2"
                                title="Scan failed" aria-label="Scan failed" data-toggle="tooltip"></i>
                            Scan failed
                        </div>
                    </div>`;
            });
        }

    });
};
