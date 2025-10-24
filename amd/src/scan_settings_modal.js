import ModalForm from 'core_form/modalform';
import {get_strings} from 'core/str';

export const init = async (contextid, cmid) => {
    const trigger = document.querySelector('#open-scan-settings');
    if (!trigger) {
        return;
    }

    // Load translations from your lang/en/plagiarism_origai.php file
    const [title, saveButton] = await get_strings([
        {key: 'scansettingstitle', component: 'plagiarism_origai'},
        {key: 'scansettingssave', component: 'plagiarism_origai'}
    ]);

    trigger.addEventListener('click', () => {
        const modal = new ModalForm({
            formClass: 'plagiarism_origai\\form\\scan_settings_form',
            args: {contextid: contextid, cmid: cmid},
            modalConfig: {title: title},
            saveButtonText: saveButton,
            returnFocus: trigger
        });

        modal.show();
    });
};
