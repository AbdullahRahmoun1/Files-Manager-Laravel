<?php

return [
    'group' => [
        'invitation' => [
            'received' => [
                'title' => 'Invitation Received',
                'body' => 'You have been invited to join the group ":groupName".',
            ],
            'refused' => [
                'title' => 'Invitation Refused',
                'body' => ':userName has refused your invitation to join the group ":groupName".',
            ],
            'accepted' => [
                'title' => 'Invitation Accepted',
                'body' => ':userName has accepted your invitation to join the group ":groupName".',
            ],
            'kicked' => [
                'title' => 'Removed from Group',
                'body' => 'You have been removed from the group ":groupName" by group admin.',
            ],
            'left' => [
                'title' => 'User Left Group',
                'body' => ':userName has left the group ":groupName".',
            ],
        ],
        'file' => [
            'check-in' => [
                'title' => 'File Checked In',
                'body' => 'The file ":fileName" has been checked in, group ":groupName".',
            ],
            'check-out' => [
                'title' => 'File Checked Out',
                'body' => 'The file ":fileName" has been checked out from, group ":groupName".',
            ],
            'created' => [
                'title' => 'File Added',
                'body' => 'A new file ":fileName" has been added to the group ":groupName".',
            ],
            'removed' => [
                'title' => 'File Removed',
                'body' => 'The file ":fileName" has been removed from the group ":groupName".',
            ],
            'add-request' => [
                'title' => 'File Addition Request',
                'body' => 'A request to add the file ":fileName" to the group ":groupName" has been made.',
            ],
            'add-request-approved' => [
                'title' => 'File Addition Approved',
                'body' => 'Your request to add the file ":fileName" has been approved and added to the group ":groupName".',
            ],
            'add-request-rejected' => [
                'title' => 'File Addition Rejected',
                'body' => 'Your request to add the file ":fileName" to the group ":groupName" has been rejected.',
            ],
        ],
    ],

];
