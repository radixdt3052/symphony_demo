// // Fetch all records from the Record table
        // $records = $recordRepository->findAll();

        // // Initialize an empty string to hold the concatenated records
        // $output = '';

        // // Iterate over each record and concatenate its data with the output string
        // foreach ($records as $record) {
        //     $output .= 'Record ID: ' . $record->getId() . ', Name: ' . $record->getName() . '<br>';
        // }

        // // Return the concatenated records in the response
        // return $this->render('demoIndex.html.twig',[
        //     'records' => $records
        // ]);