<?php

return [
    'group' => [
        'invitation' => [
            'received' => [
                'title' => 'Invitation Received',
                'message' => 'You have been invited to join the group ":groupName".',
            ],
            'refused' => [
                'title' => 'Invitation Refused',
                'message' => ':userName has refused your invitation to join the group ":groupName".',
            ],
            'accepted' => [
                'title' => 'Invitation Accepted',
                'message' => ':userName has accepted your invitation to join the group ":groupName".',
            ],
            'kicked' => [
                'title' => 'Removed from Group',
                'message' => 'You have been removed from the group ":groupName" by group admin.',
            ],
        ],
        'file' => [
            'check-in' => [
                'title' => 'File Checked In',
                'message' => 'The file ":fileName" has been checked in, group ":groupName".',
            ],
            'check-out' => [
                'title' => 'File Checked Out',
                'message' => 'The file ":fileName" has been checked out from, group ":groupName".',
            ],
            'created' => [
                'title' => 'File Added',
                'message' => 'A new file ":fileName" has been added to the group ":groupName".',
            ],
            'removed' => [
                'title' => 'File Removed',
                'message' => 'The file ":fileName" has been removed from the group ":groupName".',
            ],
            'add-request' => [
                'title' => 'File Addition Request',
                'message' => 'A request to add the file ":fileName" to the group ":groupName" has been made.',
            ],
            'add-request-approved' => [
                'title' => 'File Addition Approved',
                'message' => 'Your request to add the file ":fileName" has been approved and added to the group ":groupName".',
            ],
            'add-request-rejected' => [
                'title' => 'File Addition Rejected',
                'message' => 'Your request to add the file ":fileName" to the group ":groupName" has been rejected.',
            ],
        ],
    ],

];
