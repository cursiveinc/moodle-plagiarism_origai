# Originality.ai Moodle Plugin

This plugin leverages the API from **Originality.AI** to provide on-demand, as-needed plagiarism and AI detection. With it, you can cost-effectively provide plagiarism detection for several core Moodle activities (online text assignments, forums, and quizzes). Teachers have the ability to scan any submission and easily see the results right in Moodle or by opening the full report.

At **Cursive Technology, Inc.**, we're focused on the writing process. By capturing key event data (also known by the scary euphemism "key logging"), we can make new opportunities for teaching, learning, and research in a low-cost, low-effort way, all in the existing workflows of your course and site.

Ultimately, we believe in human contribution as captured through the writing process — the beautiful production of written work expressing your individual thoughts that cannot be completed by a third party nor replicated by generative AI. We're excited to work with you.



---

## Updates

- **05/01/24:**  
  Added backward compatibility to Moodle 3.9.

- **11/21/23:**  
  Fixes to database table names, calls, and functions to prepare for the Moodle.org plugin repository submission.

[CHANGELOG.md](./CHANGELOG.md)

---

## Contact and Referral

If you have questions or comments, please reach out to us at:  
**info@originality.ai**

If you use this plugin and purchase credits through Originality.AI, please use our referral code to support this development:  
[https://originality.ai/?lmref=IJh0Aw](https://originality.ai/?lmref=IJh0Aw)

---

## Installation

### Installing via uploaded ZIP file

1. Log in to your Moodle site as an admin and go to  
   `Site Administration > Plugins > Install plugins`.
2. Upload the ZIP file with the plugin code.  
   You should only be prompted to add extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.
4. Afterward, log in to your Moodle site as an admin and go to  
   `Site Administration > Notifications` to complete the installation.

### Alternatively

Run the following command from the Moodle root directory to complete installation from the command line:

```bash
php admin/cli/upgrade.php
```

License 2023 Cursive Technology, Inc.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see https://www.gnu.org/licenses/.